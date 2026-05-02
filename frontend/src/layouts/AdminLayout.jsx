import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function AdminLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/super-admin/login');
  };

  return (
    <div className="admin-layout">
      <nav className="admin-nav">
        <h2>Super Admin</h2>
        <div className="nav-user">{user?.name}</div>
        <ul>
          <li><Link to="/super-admin">Dashboard</Link></li>
          <li><Link to="/super-admin/schools">Schools</Link></li>
          <li><Link to="/super-admin/users">Users</Link></li>
          <li><Link to="/super-admin/plans">Plans</Link></li>
        </ul>
        <button className="btn-logout" onClick={handleLogout}>Logout</button>
      </nav>
      <main className="admin-content">
        <Outlet />
      </main>
    </div>
  );
}
