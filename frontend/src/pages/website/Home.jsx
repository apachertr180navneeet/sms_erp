import { Link } from 'react-router-dom';

export default function WebsiteHome() {
  return (
    <div className="website-home">
      <header>
        <nav>
          <h1>SaaS School ERP</h1>
          <ul>
            <li><Link to="/admin">Admin Login</Link></li>
            <li><Link to="/school">School Login</Link></li>
            <li><Link to="/portal">Portal Login</Link></li>
          </ul>
        </nav>
      </header>
      <main>
        <section className="hero">
          <h2>Complete School Management Solution</h2>
          <p>Manage students, staff, fees, exams, and more with our all-in-one platform.</p>
        </section>
        <section className="features">
          <h3>Features</h3>
          <div className="feature-grid">
            <div className="feature-card">
              <h4>Student Management</h4>
              <p>Track admissions, attendance, and performance.</p>
            </div>
            <div className="feature-card">
              <h4>Fee Management</h4>
              <p>Automated fee collection and receipts.</p>
            </div>
            <div className="feature-card">
              <h4>Exam & Results</h4>
              <p>Schedule exams and generate report cards.</p>
            </div>
            <div className="feature-card">
              <h4>Website Builder</h4>
              <p>Every school gets a customizable website.</p>
            </div>
          </div>
        </section>
      </main>
      <footer>
        <p>&copy; 2026 SaaS School ERP. All rights reserved.</p>
      </footer>
    </div>
  );
}
