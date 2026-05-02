import { Outlet, Link } from 'react-router-dom';

export default function PortalLayout() {
  return (
    <div className="portal-layout">
      <nav className="portal-nav">
        <h2>Portal</h2>
        <ul>
          <li><Link to="/portal">Dashboard</Link></li>
          <li><Link to="/portal/profile">Profile</Link></li>
          <li><Link to="/portal/attendance">Attendance</Link></li>
          <li><Link to="/portal/results">Results</Link></li>
        </ul>
      </nav>
      <main className="portal-content">
        <Outlet />
      </main>
    </div>
  );
}
