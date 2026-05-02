import { useState, useEffect } from 'react';
import api from '../../config/api';

export default function Plans() {
  const [plans, setPlans] = useState([]);
  const [modules, setModules] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState(null);
  const [formData, setFormData] = useState({
    name: '', description: '', price: '', student_limit: '', staff_limit: '',
    storage_limit_mb: '', billing_cycle: 'monthly', features: '', is_active: true, module_ids: []
  });
  const [error, setError] = useState('');

  useEffect(() => {
    fetchPlans();
    api.get('/admin/modules').then(res => setModules(res.data.modules)).catch(() => {});
  }, []);

  const fetchPlans = async () => {
    try {
      const res = await api.get('/admin/plans');
      setPlans(res.data.plans);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    try {
      const data = {
        ...formData,
        price: parseFloat(formData.price) || 0,
        student_limit: parseInt(formData.student_limit) || 0,
        staff_limit: parseInt(formData.staff_limit) || 0,
        storage_limit_mb: parseInt(formData.storage_limit_mb) || 1024,
        features: formData.features.split('\n').filter(f => f.trim()),
      };
      if (editing) {
        await api.put(`/admin/plans/${editing.id}`, data);
      } else {
        await api.post('/admin/plans', data);
      }
      resetForm();
      fetchPlans();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed');
    }
  };

  const handleEdit = (plan) => {
    setEditing(plan);
    setFormData({
      name: plan.name, description: plan.description || '', price: plan.price,
      student_limit: plan.student_limit, staff_limit: plan.staff_limit,
      storage_limit_mb: plan.storage_limit_mb, billing_cycle: plan.billing_cycle,
      features: Array.isArray(plan.features) ? plan.features.join('\n') : '',
      is_active: plan.is_active,
      module_ids: Array.isArray(plan.modules) ? plan.modules.map(m => m.id) : [],
    });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (!confirm('Delete this plan?')) return;
    await api.delete(`/admin/plans/${id}`);
    fetchPlans();
  };

  const resetForm = () => {
    setShowForm(false);
    setEditing(null);
    setFormData({ name: '', description: '', price: '', student_limit: '', staff_limit: '', storage_limit_mb: '', billing_cycle: 'monthly', features: '', is_active: true, module_ids: [] });
  };

  const toggleModule = (modId) => {
    const ids = formData.module_ids.includes(modId)
      ? formData.module_ids.filter(id => id !== modId)
      : [...formData.module_ids, modId];
    setFormData({ ...formData, module_ids: ids });
  };

  const toggleStatus = async (plan) => {
    await api.put(`/admin/plans/${plan.id}`, { is_active: !plan.is_active });
    fetchPlans();
  };

  return (
    <div>
      <div className="page-header">
        <h1>Subscription Plans</h1>
        <button className="btn btn-primary" onClick={() => setShowForm(true)}>+ Add Plan</button>
      </div>

      {showForm && (
        <div className="form-card">
          <h3>{editing ? 'Edit Plan' : 'New Plan'}</h3>
          {error && <div className="error-message">{error}</div>}
          <form onSubmit={handleSubmit}>
            <div className="form-grid">
              <div className="form-group"><label>Name *</label>
                <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} required /></div>
              <div className="form-group"><label>Price *</label>
                <input type="number" step="0.01" value={formData.price} onChange={(e) => setFormData({ ...formData, price: e.target.value })} required /></div>
              <div className="form-group"><label>Billing Cycle</label>
                <select value={formData.billing_cycle} onChange={(e) => setFormData({ ...formData, billing_cycle: e.target.value })}>
                  <option value="monthly">Monthly</option><option value="yearly">Yearly</option>
                </select></div>
              <div className="form-group"><label>Student Limit (0=unlimited)</label>
                <input type="number" value={formData.student_limit} onChange={(e) => setFormData({ ...formData, student_limit: e.target.value })} /></div>
              <div className="form-group"><label>Staff Limit (0=unlimited)</label>
                <input type="number" value={formData.staff_limit} onChange={(e) => setFormData({ ...formData, staff_limit: e.target.value })} /></div>
              <div className="form-group"><label>Storage (MB)</label>
                <input type="number" value={formData.storage_limit_mb} onChange={(e) => setFormData({ ...formData, storage_limit_mb: e.target.value })} /></div>
              <div className="form-group"><label className="toggle-label">
                <input type="checkbox" checked={formData.is_active} onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })} /> Active</label></div>
              <div className="form-group full-width"><label>Description</label>
                <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows="2" /></div>
              <div className="form-group full-width"><label>Features (one per line)</label>
                <textarea value={formData.features} onChange={(e) => setFormData({ ...formData, features: e.target.value })} rows="3" /></div>
              <div className="form-group full-width">
                <label>Include Modules</label>
                <div className="module-checks">
                  {modules.map(mod => (
                    <label key={mod.id} className="check-item">
                      <input type="checkbox" checked={formData.module_ids.includes(mod.id)} onChange={() => toggleModule(mod.id)} />
                      {mod.name}
                    </label>
                  ))}
                </div>
              </div>
            </div>
            <div className="form-actions">
              <button type="button" className="btn btn-secondary" onClick={resetForm}>Cancel</button>
              <button type="submit" className="btn btn-primary">{editing ? 'Update' : 'Create'}</button>
            </div>
          </form>
        </div>
      )}

      <div className="plans-grid">
        {loading ? <div className="loading-state">Loading...</div> :
          plans.map(plan => (
            <div key={plan.id} className={`plan-card ${!plan.is_active ? 'plan-inactive' : ''}`}>
              <div className="plan-header">
                <h3>{plan.name}</h3>
                <span className={`badge ${plan.is_active ? 'badge-active' : 'badge-inactive'}`}>{plan.is_active ? 'Active' : 'Inactive'}</span>
              </div>
              <div className="plan-price">${plan.price}<span>/{plan.billing_cycle}</span></div>
              <ul className="plan-limits">
                <li>{plan.student_limit > 0 ? `${plan.student_limit} students` : 'Unlimited students'}</li>
                <li>{plan.staff_limit > 0 ? `${plan.staff_limit} staff` : 'Unlimited staff'}</li>
                <li>{plan.storage_limit_mb >= 1024 ? `${plan.storage_limit_mb / 1024} GB storage` : `${plan.storage_limit_mb} MB storage`}</li>
              </ul>
              <div className="plan-modules">
                {Array.isArray(plan.modules) ? plan.modules.map(m => <span key={m.id} className="module-tag">{m.name}</span>) : null}
              </div>
              <div className="plan-actions">
                <button className="btn-sm" onClick={() => toggleStatus(plan)}>{plan.is_active ? 'Disable' : 'Enable'}</button>
                <button className="btn-sm btn-edit" onClick={() => handleEdit(plan)}>Edit</button>
                <button className="btn-sm btn-danger" onClick={() => handleDelete(plan.id)}>Delete</button>
              </div>
            </div>
          ))}
      </div>
    </div>
  );
}
