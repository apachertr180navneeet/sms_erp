import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function SchoolLayout() {
  const { user, logout, hasRole } = useAuth();
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
          <li><Link to="/school-admin">Dashboard</Link></li>
          {hasRole('school_admin') && (
            <>
              <li><Link to="/school-admin/settings">Settings</Link></li>
              <li><Link to="/school-admin/staff">Staff</Link></li>
            </>
          )}
          <li><Link to="/school-admin/students">Students</Link></li>
          <li><Link to="/school-admin/fees">Fees</Link></li>
          <li><Link to="/school-admin/exams">Exams</Link></li>
        </ul>
        <button className="btn-logout" onClick={handleLogout}>Logout</button>
      </nav>
      <main className="school-content">
        <Outlet />
      </main>
    </div>
  );
}
