import { useState, useEffect } from 'react';
import api from '../../config/api';

export default function Modules() {
  const [modules, setModules] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState(null);
  const [formData, setFormData] = useState({ name: '', description: '', icon: '', is_active: true, sort_order: 0 });
  const [error, setError] = useState('');

  useEffect(() => { fetchModules(); }, []);

  const fetchModules = async () => {
    try {
      const res = await api.get('/admin/modules');
      setModules(res.data.modules);
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
      if (editing) {
        await api.put(`/admin/modules/${editing.id}`, formData);
      } else {
        await api.post('/admin/modules', formData);
      }
      resetForm();
      fetchModules();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed');
    }
  };

  const handleEdit = (mod) => {
    setEditing(mod);
    setFormData({ name: mod.name, description: mod.description || '', icon: mod.icon || '', is_active: mod.is_active, sort_order: mod.sort_order });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (!confirm('Delete this module?')) return;
    await api.delete(`/admin/modules/${id}`);
    fetchModules();
  };

  const resetForm = () => {
    setShowForm(false);
    setEditing(null);
    setFormData({ name: '', description: '', icon: '', is_active: true, sort_order: 0 });
  };

  const toggleStatus = async (mod) => {
    await api.put(`/admin/modules/${mod.id}`, { is_active: !mod.is_active });
    fetchModules();
  };

  return (
    <div>
      <div className="page-header">
        <h1>Modules</h1>
        <button className="btn btn-primary" onClick={() => setShowForm(true)}>+ Add Module</button>
      </div>

      {showForm && (
        <div className="form-card">
          <h3>{editing ? 'Edit Module' : 'New Module'}</h3>
          {error && <div className="error-message">{error}</div>}
          <form onSubmit={handleSubmit}>
            <div className="form-grid">
              <div className="form-group">
                <label>Name *</label>
                <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} required />
              </div>
              <div className="form-group">
                <label>Icon (class)</label>
                <input type="text" value={formData.icon} onChange={(e) => setFormData({ ...formData, icon: e.target.value })} />
              </div>
              <div className="form-group">
                <label>Sort Order</label>
                <input type="number" value={formData.sort_order} onChange={(e) => setFormData({ ...formData, sort_order: parseInt(e.target.value) })} />
              </div>
              <div className="form-group">
                <label className="toggle-label">
                  <input type="checkbox" checked={formData.is_active} onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })} />
                  Active
                </label>
              </div>
              <div className="form-group full-width">
                <label>Description</label>
                <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows="2" />
              </div>
            </div>
            <div className="form-actions">
              <button type="button" className="btn btn-secondary" onClick={resetForm}>Cancel</button>
              <button type="submit" className="btn btn-primary">{editing ? 'Update' : 'Create'}</button>
            </div>
          </form>
        </div>
      )}

      <div className="table-card">
        {loading ? (
          <div className="loading-state">Loading...</div>
        ) : (
          <table className="data-table">
            <thead>
              <tr><th>Icon</th><th>Name</th><th>Description</th><th>Sort</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              {modules.map(mod => (
                <tr key={mod.id}>
                  <td>{mod.icon}</td>
                  <td><strong>{mod.name}</strong></td>
                  <td>{mod.description || '—'}</td>
                  <td>{mod.sort_order}</td>
                  <td>
                    <span className={`badge ${mod.is_active ? 'badge-active' : 'badge-inactive'}`}>
                      {mod.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td className="actions-cell">
                    <button className="btn-sm" onClick={() => toggleStatus(mod)}>{mod.is_active ? 'Disable' : 'Enable'}</button>
                    <button className="btn-sm btn-edit" onClick={() => handleEdit(mod)}>Edit</button>
                    <button className="btn-sm btn-danger" onClick={() => handleDelete(mod.id)}>Delete</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
