import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function PortalLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="portal-layout">
      <nav className="portal-nav">
        <h2>Portal</h2>
        <div className="nav-user">{user?.name}</div>
        <ul>
          <li><Link to="/portal">Dashboard</Link></li>
          <li><Link to="/portal/profile">Profile</Link></li>
          <li><Link to="/portal/attendance">Attendance</Link></li>
          <li><Link to="/portal/results">Results</Link></li>
        </ul>
        <button className="btn-logout" onClick={handleLogout}>Logout</button>
      </nav>
      <main className="portal-content">
        <Outlet />
      </main>
    </div>
  );
}
