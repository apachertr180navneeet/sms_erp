import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export default function RegisterSchool() {
  const [formData, setFormData] = useState({
    schoolName: '',
    schoolEmail: '',
    schoolPhone: '',
    adminName: '',
    adminEmail: '',
    adminPassword: '',
    adminPasswordConfirmation: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { registerSchool, getRedirectPath } = useAuth();
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (formData.adminPassword !== formData.adminPasswordConfirmation) {
      setError('Passwords do not match');
      return;
    }

    setLoading(true);

    try {
      await registerSchool(formData);
      navigate(getRedirectPath());
    } catch (err) {
      setError(err.response?.data?.message || 'School registration failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-page">
      <div className="auth-container auth-container-lg">
        <h2>Register Your School</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="error-message">{error}</div>}
          <h3>School Details</h3>
          <div className="form-group">
            <label>School Name</label>
            <input type="text" name="schoolName" value={formData.schoolName} onChange={handleChange} required />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label>School Email</label>
              <input type="email" name="schoolEmail" value={formData.schoolEmail} onChange={handleChange} />
            </div>
            <div className="form-group">
              <label>School Phone</label>
              <input type="text" name="schoolPhone" value={formData.schoolPhone} onChange={handleChange} />
            </div>
          </div>
          <h3>Admin Account</h3>
          <div className="form-group">
            <label>Admin Name</label>
            <input type="text" name="adminName" value={formData.adminName} onChange={handleChange} required />
          </div>
          <div className="form-group">
            <label>Admin Email</label>
            <input type="email" name="adminEmail" value={formData.adminEmail} onChange={handleChange} required />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label>Admin Password</label>
              <input type="password" name="adminPassword" value={formData.adminPassword} onChange={handleChange} required />
            </div>
            <div className="form-group">
              <label>Confirm Password</label>
              <input type="password" name="adminPasswordConfirmation" value={formData.adminPasswordConfirmation} onChange={handleChange} required />
            </div>
          </div>
          <button type="submit" disabled={loading}>
            {loading ? 'Registering School...' : 'Register School'}
          </button>
        </form>
        <p className="auth-link">
          Already have an account? <Link to="/login">Login</Link>
        </p>
      </div>
    </div>
  );
}
