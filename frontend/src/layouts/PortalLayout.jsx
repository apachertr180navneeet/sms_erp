import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const navItems = [
  { to: '/portal', label: 'Dashboard', exact: true },
  { to: '/portal/profile', label: 'Profile' },
  { to: '/portal/attendance', label: 'Attendance' },
  { to: '/portal/results', label: 'Results' },
];

function isActive(to, exact) {
  const path = window.location.pathname;
  return exact ? path === to : path.startsWith(to);
}

export default function PortalLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/portal/login');
  };

  return (
    <div className="portal-layout">
      <nav className="portal-nav">
        <h2>Portal</h2>
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
      <main className="portal-content">
        <Outlet />
      </main>
    </div>
  );
}
