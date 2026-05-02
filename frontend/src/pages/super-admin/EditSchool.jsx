import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import api from '../../config/api';

export default function EditSchool() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [plans, setPlans] = useState([]);
  const [school, setSchool] = useState(null);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(true);
  const [error, setError] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    address: '',
    subdomain: '',
    plan_id: '',
    is_active: true,
  });

  useEffect(() => {
    api.get('/plans').then(res => setPlans(res.data.plans || [])).catch(() => {});
    api.get(`/admin/schools/${id}`).then(res => {
      const s = res.data.school;
      setSchool(s);
      setFormData({
        name: s.name || '',
        email: s.email || '',
        phone: s.phone || '',
        address: s.address || '',
        subdomain: s.subdomain || '',
        plan_id: s.plan_id || '',
        is_active: s.is_active,
      });
    }).catch(() => setError('School not found')).finally(() => setFetching(false));
  }, [id]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({ ...formData, [name]: type === 'checkbox' ? checked : value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await api.put(`/admin/schools/${id}`, {
        ...formData,
        plan_id: formData.plan_id || null,
      });
      navigate('/super-admin/schools');
    } catch (err) {
      const errors = err.response?.data?.errors;
      if (errors) {
        setError(Object.values(errors).flat().join(', '));
      } else {
        setError(err.response?.data?.message || 'Failed to update school');
      }
    } finally {
      setLoading(false);
    }
  };

  if (fetching) return <div className="loading-state">Loading...</div>;
  if (error && !school) return <div className="error-message">{error}</div>;

  return (
    <div>
      <div className="page-header">
        <h1>Edit School</h1>
        <Link to="/super-admin/schools" className="btn btn-secondary">Back</Link>
      </div>

      <form onSubmit={handleSubmit} className="form-card">
        {error && <div className="error-message">{error}</div>}

        <div className="form-grid">
          <div className="form-group full-width">
            <label>School Name *</label>
            <input type="text" name="name" value={formData.name} onChange={handleChange} required />
          </div>
          <div className="form-group">
            <label>Subdomain</label>
            <div className="input-with-suffix">
              <input type="text" name="subdomain" value={formData.subdomain} onChange={handleChange} placeholder="school-name" />
              <span className="input-suffix">.yourdomain.com</span>
            </div>
          </div>
          <div className="form-group">
            <label>Email</label>
            <input type="email" name="email" value={formData.email} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value={formData.phone} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label>Subscription Plan</label>
            <select name="plan_id" value={formData.plan_id} onChange={handleChange}>
              <option value="">No Plan</option>
              {plans.map(p => (
                <option key={p.id} value={p.id}>{p.name} - ${p.price}/{p.billing_cycle}</option>
              ))}
            </select>
          </div>
          <div className="form-group full-width">
            <label>Address</label>
            <textarea name="address" value={formData.address} onChange={handleChange} rows="3" />
          </div>
          <div className="form-group">
            <label className="toggle-label">
              <input type="checkbox" name="is_active" checked={formData.is_active} onChange={handleChange} />
              Active
            </label>
          </div>
        </div>

        <div className="form-actions">
          <button type="button" className="btn btn-secondary" onClick={() => navigate(-1)}>Cancel</button>
          <button type="submit" disabled={loading} className="btn btn-primary">
            {loading ? 'Saving...' : 'Save Changes'}
          </button>
        </div>
      </form>
    </div>
  );
}
