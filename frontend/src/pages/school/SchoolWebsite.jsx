import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../config/api';
import { getSubdomain } from '../../config/subdomain';

export default function SchoolWebsite() {
  const [school, setSchool] = useState(null);
  const [loading, setLoading] = useState(true);
  const subdomain = getSubdomain();

  useEffect(() => {
    const loadSchool = async () => {
      try {
        console.log('Fetching school for subdomain:', subdomain);
        const res = await api.get('/schools/by-subdomain', { params: { subdomain } });
        setSchool(res.data.school);
      } catch (err) {
        console.error('Failed to load school:', err.response?.status, err.response?.data);
        setSchool(null);
      } finally {
        setLoading(false);
      }
    };
    if (subdomain) loadSchool();
    else setLoading(false);
  }, [subdomain]);

  return (
    <div className="school-website">
      <header className="sw-header">
        <nav className="sw-nav">
          <div className="sw-logo">{school?.name || (subdomain ? subdomain.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) + ' School' : 'School Portal')}</div>
          <div className="sw-nav-links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
            <Link to="/login" className="sw-btn">Login</Link>
          </div>
        </nav>
        <div className="sw-hero" id="home">
          <h1>{school ? `Welcome to ${school.name}` : 'Welcome to Our School'}</h1>
          <p>Empowering students to achieve excellence in education</p>
          <Link to="/login" className="sw-btn sw-btn-lg">Student Portal</Link>
        </div>
      </header>

      {loading ? (
        <div className="sw-section" style={{textAlign:'center'}}><p>Loading...</p></div>
      ) : school ? (
        <>
          <section id="about" className="sw-section">
            <div className="sw-section-inner">
              <h2>About Us</h2>
              <p>{school.name} is committed to providing quality education in a nurturing environment.</p>
              <div className="sw-stats">
                <div className="sw-stat"><span className="sw-stat-num">500+</span><span className="sw-stat-label">Students</span></div>
                <div className="sw-stat"><span className="sw-stat-num">50+</span><span className="sw-stat-label">Teachers</span></div>
                <div className="sw-stat"><span className="sw-stat-num">10+</span><span className="sw-stat-label">Years</span></div>
              </div>
            </div>
          </section>

          <section className="sw-section sw-section-alt">
            <div className="sw-section-inner">
              <h2>Our Programs</h2>
              <div className="sw-grid">
                <div className="sw-card"><h3>Primary</h3><p>Foundation learning for young minds</p></div>
                <div className="sw-card"><h3>Middle</h3><p>Building knowledge and critical thinking</p></div>
                <div className="sw-card"><h3>Secondary</h3><p>Preparing students for higher education</p></div>
              </div>
            </div>
          </section>

          <section id="contact" className="sw-section">
            <div className="sw-section-inner">
              <h2>Contact Us</h2>
              <div className="sw-contact">
                {school.address && <div><strong>Address:</strong> {school.address}</div>}
                {school.email && <div><strong>Email:</strong> {school.email}</div>}
                {school.phone && <div><strong>Phone:</strong> {school.phone}</div>}
              </div>
            </div>
          </section>
        </>
      ) : (
        <div className="sw-section" style={{textAlign:'center'}}>
          <h2>School Website</h2>
          <p>This school website is under construction. Please check back later or contact administration.</p>
        </div>
      )}

      <footer className="sw-footer">
        <p>&copy; {new Date().getFullYear()} {school?.name || 'School'}. All rights reserved.</p>
      </footer>
    </div>
  );
}
