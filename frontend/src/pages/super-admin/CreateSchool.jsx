import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../../config/api';

export default function CreateSchool() {
  const navigate = useNavigate();
  const [plans, setPlans] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    address: '',
    subdomain: '',
    url: '',
    plan_id: '',
    create_admin: true,
    admin_name: '',
    admin_email: '',
    admin_password: '',
    admin_password_confirmation: '',
  });

  useEffect(() => {
    api.get('/plans').then(res => setPlans(res.data.plans || [])).catch(() => {});
  }, []);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({ ...formData, [name]: type === 'checkbox' ? checked : value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (formData.create_admin && formData.admin_password !== formData.admin_password_confirmation) {
      setError('Passwords do not match');
      return;
    }

    setLoading(true);

    try {
      await api.post('/admin/schools', {
        ...formData,
        plan_id: formData.plan_id || null,
      });
      navigate('/super-admin/schools');
    } catch (err) {
      const errors = err.response?.data?.errors;
      if (errors) {
        setError(Object.values(errors).flat().join(', '));
      } else {
        setError(err.response?.data?.message || 'Failed to create school');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <div className="page-header">
        <h1>Add New School</h1>
        <Link to="/super-admin/schools" className="btn btn-secondary">Back to Schools</Link>
      </div>

      <form onSubmit={handleSubmit} className="form-card">
        {error && <div className="error-message">{error}</div>}

        <section className="form-section">
          <h3>School Details</h3>
          <div className="form-grid">
            <div className="form-group">
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
            <div className="form-group full-width">
              <label>Address</label>
              <textarea name="address" value={formData.address} onChange={handleChange} rows="2" />
            </div>
            <div className="form-group full-width">
              <label>School Website URL</label>
              <input type="url" name="url" value={formData.url} onChange={handleChange} placeholder="https://schoolname.com" />
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
          </div>
        </section>

        <section className="form-section">
          <div className="form-header">
            <h3>Create Admin Account</h3>
            <label className="toggle-label">
              <input type="checkbox" name="create_admin" checked={formData.create_admin} onChange={handleChange} />
              Create admin user
            </label>
          </div>

          {formData.create_admin && (
            <div className="form-grid">
              <div className="form-group">
                <label>Admin Name *</label>
                <input type="text" name="admin_name" value={formData.admin_name} onChange={handleChange} required />
              </div>
              <div className="form-group">
                <label>Admin Email *</label>
                <input type="email" name="admin_email" value={formData.admin_email} onChange={handleChange} required />
              </div>
              <div className="form-group">
                <label>Password *</label>
                <input type="password" name="admin_password" value={formData.admin_password} onChange={handleChange} required />
              </div>
              <div className="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="admin_password_confirmation" value={formData.admin_password_confirmation} onChange={handleChange} required />
              </div>
            </div>
          )}
        </section>

        <div className="form-actions">
          <button type="submit" disabled={loading} className="btn btn-primary">
            {loading ? 'Creating...' : 'Create School'}
          </button>
        </div>
      </form>
    </div>
  );
}
