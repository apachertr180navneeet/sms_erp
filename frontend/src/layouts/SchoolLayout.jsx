import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const navItems = [
  { to: '/school-admin', label: 'Dashboard', exact: true },
  { to: '/school-admin/students', label: 'Students' },
  { to: '/school-admin/fees', label: 'Fees' },
  { to: '/school-admin/exams', label: 'Exams' },
];

function isActive(to, exact) {
  const path = window.location.pathname;
  return exact ? path === to : path.startsWith(to);
}

export default function SchoolLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/school-admin/login');
  };

  return (
    <div className="school-layout">
      <nav className="school-nav">
        <h2>School ERP</h2>
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
      <main className="school-content">
        <Outlet />
      </main>
    </div>
  );
}
