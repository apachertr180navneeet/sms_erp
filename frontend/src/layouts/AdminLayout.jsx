import { Outlet, Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const navItems = [
  { to: '/super-admin', label: 'Dashboard', exact: true },
  { to: '/super-admin/schools', label: 'Schools' },
  { to: '/super-admin/modules', label: 'Modules' },
  { to: '/super-admin/plans', label: 'Plans' },
  { to: '/super-admin/users', label: 'Users' },
];

function isActive(to, exact) {
  const path = window.location.pathname;
  return exact ? path === to : path.startsWith(to);
}

export default function AdminLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

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
          {navItems.map(item => (
            <li key={item.to}>
              <Link
                to={item.to}
                className={isActive(item.to, item.exact) ? 'active' : ''}
              >
                {item.label}
              </Link>
            </li>
          ))}
        </ul>
        <button className="btn-logout" onClick={handleLogout}>Logout</button>
      </nav>
      <main className="admin-content">
        <Outlet />
      </main>
    </div>
  );
}
