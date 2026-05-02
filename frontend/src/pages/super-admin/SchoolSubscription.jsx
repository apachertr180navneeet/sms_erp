import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import api from '../../config/api';

export default function SchoolSubscription() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [school, setSchool] = useState(null);
  const [subscriptions, setSubscriptions] = useState([]);
  const [modules, setModules] = useState([]);
  const [plans, setPlans] = useState([]);
  const [schoolModules, setSchoolModules] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showSubForm, setShowSubForm] = useState(false);
  const [subData, setSubData] = useState({ plan_id: '', amount: '', start_date: '', end_date: '', payment_method: '', transaction_id: '', notes: '' });
  const [error, setError] = useState('');

  useEffect(() => {
    fetchData();
  }, [id]);

  const fetchData = async () => {
    try {
      const [schoolRes, subRes, modulesRes, plansRes, schoolModsRes] = await Promise.all([
        api.get(`/admin/schools/${id}`),
        api.get(`/admin/subscriptions?school_id=${id}`),
        api.get('/admin/modules'),
        api.get('/admin/plans'),
        api.get(`/admin/schools/${id}/modules`),
      ]);
      setSchool(schoolRes.data.school);
      setSubscriptions(subRes.data.subscriptions?.data || []);
      setModules(modulesRes.data.modules);
      setPlans(plansRes.data.plans);
      setSchoolModules(schoolModsRes.data.modules || []);
    } catch (err) {
      setError('Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const createSubscription = async (e) => {
    e.preventDefault();
    setError('');
    try {
      await api.post('/admin/subscriptions', {
        school_id: parseInt(id),
        ...subData,
        amount: parseFloat(subData.amount) || 0,
      });
      setShowSubForm(false);
      setSubData({ plan_id: '', amount: '', start_date: '', end_date: '', payment_method: '', transaction_id: '', notes: '' });
      fetchData();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed');
    }
  };

  const toggleSchoolModule = async (modId, current) => {
    const updated = schoolModules.map(m => m.id === modId ? { ...m, is_enabled: !current } : m);
    setSchoolModules(updated);
    await api.put(`/admin/schools/${id}/modules`, { modules: updated });
  };

  if (loading) return <div className="loading-state">Loading...</div>;
  if (!school) return <div className="empty-state">School not found</div>;

  const activeSub = subscriptions.find(s => s.status === 'active' && new Date(s.end_date) > new Date());

  return (
    <div>
      <div className="page-header">
        <h1>{school.name} — Subscription</h1>
        <Link to="/super-admin/schools" className="btn btn-secondary">Back</Link>
      </div>

      <div className="detail-grid">
        <div className="detail-card">
          <h3>Subscription Status</h3>
          {activeSub ? (
            <>
              <div className="detail-row"><span className="detail-label">Plan</span><span className="detail-value">{activeSub.plan?.name}</span></div>
              <div className="detail-row"><span className="detail-label">Amount</span><span className="detail-value">${activeSub.amount}</span></div>
              <div className="detail-row"><span className="detail-label">Start</span><span className="detail-value">{new Date(activeSub.start_date).toLocaleDateString()}</span></div>
              <div className="detail-row"><span className="detail-label">End</span><span className="detail-value">{new Date(activeSub.end_date).toLocaleDateString()}</span></div>
              <div className="detail-row"><span className="detail-label">Status</span><span className="detail-value"><span className="badge badge-active">Active</span></span></div>
            </>
          ) : (
            <div className="empty-state">No active subscription</div>
          )}
          <button className="btn btn-primary" style={{ marginTop: 16 }} onClick={() => setShowSubForm(!showSubForm)}>
            {showSubForm ? 'Cancel' : '+ New Subscription'}
          </button>
        </div>

        <div className="detail-card">
          <h3>Quick Info</h3>
          <div className="detail-row"><span className="detail-label">School Code</span><span className="detail-value"><code>{school.code}</code></span></div>
          <div className="detail-row"><span className="detail-label">Current Plan</span><span className="detail-value">{school.plan?.name || '—'}</span></div>
          <div className="detail-row"><span className="detail-label">Sub. Ends</span><span className="detail-value">{school.subscription_ends_at ? new Date(school.subscription_ends_at).toLocaleDateString() : '—'}</span></div>
          <div className="detail-row"><span className="detail-label">Modules Enabled</span><span className="detail-value">{schoolModules.filter(m => m.is_enabled).length} / {schoolModules.length}</span></div>
        </div>
      </div>

      {showSubForm && (
        <div className="form-card">
          <h3>Create Subscription</h3>
          {error && <div className="error-message">{error}</div>}
          <form onSubmit={createSubscription}>
            <div className="form-grid">
              <div className="form-group"><label>Plan *</label>
                <select value={subData.plan_id} onChange={(e) => {
                  setSubData({ ...subData, plan_id: e.target.value });
                  const plan = plans.find(p => p.id == e.target.value);
                  if (plan) setSubData(prev => ({ ...prev, amount: plan.price }));
                }} required>
                  <option value="">Select Plan</option>
                  {plans.map(p => <option key={p.id} value={p.id}>{p.name} - ${p.price}/{p.billing_cycle}</option>)}
                </select></div>
              <div className="form-group"><label>Amount</label>
                <input type="number" step="0.01" value={subData.amount} onChange={(e) => setSubData({ ...subData, amount: e.target.value })} /></div>
              <div className="form-group"><label>Start Date *</label>
                <input type="date" value={subData.start_date} onChange={(e) => setSubData({ ...subData, start_date: e.target.value })} required /></div>
              <div className="form-group"><label>End Date *</label>
                <input type="date" value={subData.end_date} onChange={(e) => setSubData({ ...subData, end_date: e.target.value })} required /></div>
              <div className="form-group"><label>Payment Method</label>
                <input type="text" value={subData.payment_method} onChange={(e) => setSubData({ ...subData, payment_method: e.target.value })} /></div>
              <div className="form-group"><label>Transaction ID</label>
                <input type="text" value={subData.transaction_id} onChange={(e) => setSubData({ ...subData, transaction_id: e.target.value })} /></div>
              <div className="form-group full-width"><label>Notes</label>
                <textarea value={subData.notes} onChange={(e) => setSubData({ ...subData, notes: e.target.value })} rows="2" /></div>
            </div>
            <div className="form-actions">
              <button type="submit" className="btn btn-primary">Create Subscription</button>
            </div>
          </form>
        </div>
      )}

      <div className="detail-card" style={{ marginTop: 24 }}>
        <h3>Modules</h3>
        <div className="modules-grid">
          {schoolModules.map(mod => (
            <div key={mod.id} className={`module-item ${mod.is_enabled ? 'module-enabled' : ''}`}>
              <label className="toggle-label">
                <input type="checkbox" checked={mod.is_enabled} onChange={() => toggleSchoolModule(mod.id, mod.is_enabled)} />
                {mod.icon && <span className="module-icon">{mod.icon}</span>}
                {mod.name}
              </label>
            </div>
          ))}
        </div>
      </div>

      {subscriptions.length > 0 && (
        <div className="table-card" style={{ marginTop: 24 }}>
          <h3 className="table-header"><span>Subscription History</span></h3>
          <table className="data-table">
            <thead><tr><th>Plan</th><th>Amount</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
            <tbody>
              {subscriptions.map(sub => (
                <tr key={sub.id}>
                  <td>{sub.plan?.name}</td>
                  <td>${sub.amount}</td>
                  <td>{new Date(sub.start_date).toLocaleDateString()}</td>
                  <td>{new Date(sub.end_date).toLocaleDateString()}</td>
                  <td><span className={`badge ${sub.status === 'active' ? 'badge-active' : 'badge-inactive'}`}>{sub.status}</span></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
