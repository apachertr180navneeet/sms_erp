import { Link } from 'react-router-dom';

export default function Unauthorized() {
  return (
    <div className="unauthorized-page">
      <div className="auth-container">
        <h2>Access Denied</h2>
        <p>You do not have permission to access this page.</p>
        <Link to="/" className="btn">Go to Home</Link>
      </div>
    </div>
  );
}
