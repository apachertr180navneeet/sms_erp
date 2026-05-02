import { Outlet, Link } from 'react-router-dom';

export default function SchoolLayout() {
  return (
    <div className="school-layout">
      <nav className="school-nav">
        <h2>School ERP</h2>
        <ul>
          <li><Link to="/school">Dashboard</Link></li>
          <li><Link to="/school/students">Students</Link></li>
          <li><Link to="/school/staff">Staff</Link></li>
          <li><Link to="/school/fees">Fees</Link></li>
          <li><Link to="/school/exams">Exams</Link></li>
        </ul>
      </nav>
      <main className="school-content">
        <Outlet />
      </main>
    </div>
  );
}
