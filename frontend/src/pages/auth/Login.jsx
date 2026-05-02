import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { getSubdomain } from '../../config/subdomain';

export default function Login({ redirectRole, schoolAdminOnly = false }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { login, hasRole } = useAuth();
  const navigate = useNavigate();
  const subdomain = getSubdomain();
  const isSubdomainLogin = schoolAdminOnly && subdomain;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await login(email, password);

      if (redirectRole && !hasRole(redirectRole)) {
        setError(`Access denied. This login is for ${redirectRole} only.`);
        setLoading(false);
        return;
      }

      if (schoolAdminOnly && !hasRole('school_admin')) {
        setError('Access denied. This login is for school admins only.');
        setLoading(false);
        return;
      }

      navigate(redirectRole === 'super_admin' ? '/super-admin' : '/school-admin');
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  const title = isSubdomainLogin ? 'School Portal Login' : schoolAdminOnly ? 'School Admin Login' : 'Super Admin Login';
  const mainLink = schoolAdminOnly ? '/super-admin/login' : '/school-admin/login';
  const mainLinkText = schoolAdminOnly ? 'Super Admin' : 'School Admin';

  return (
    <div className="auth-page">
      <div className="auth-container">
        {isSubdomainLogin && <div className="school-badge">{subdomain}</div>}
        <h2>{title}</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="error-message">{error}</div>}
          <div className="form-group">
            <label>Email</label>
            <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
          </div>
          <div className="form-group">
            <label>Password</label>
            <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
          </div>
          <button type="submit" disabled={loading}>
            {loading ? 'Logging in...' : 'Login'}
          </button>
        </form>
        {!isSubdomainLogin && (
          <>
            <p className="auth-link">
              Looking for {mainLinkText} login? <Link to={mainLink}>{mainLinkText} Login</Link>
            </p>
            <p className="auth-link">
              Want to register a school? <Link to="/register-school">Register School</Link>
            </p>
          </>
        )}
      </div>
    </div>
  );
}
