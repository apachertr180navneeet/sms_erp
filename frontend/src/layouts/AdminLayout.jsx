import { Outlet, Link } from 'react-router-dom';

export default function AdminLayout() {
  return (
    <div className="admin-layout">
      <nav className="admin-nav">
        <h2>Super Admin Panel</h2>
        <ul>
          <li><Link to="/admin">Dashboard</Link></li>
          <li><Link to="/admin/schools">Schools</Link></li>
          <li><Link to="/admin/users">Users</Link></li>
          <li><Link to="/admin/plans">Plans</Link></li>
        </ul>
      </nav>
      <main className="admin-content">
        <Outlet />
      </main>
    </div>
  );
}
