import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../config/api';

export default function Schools() {
  const [schools, setSchools] = useState([]);
  const [stats, setStats] = useState({ total: 0, active: 0, inactive: 0 });
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');

  useEffect(() => {
    fetchSchools();
    fetchStats();
  }, []);

  const fetchSchools = async () => {
    try {
      const res = await api.get('/admin/schools');
      setSchools(res.data.schools?.data || []);
    } catch (err) {
      console.error('Failed to fetch schools', err);
    } finally {
      setLoading(false);
    }
  };

  const fetchStats = async () => {
    try {
      const res = await api.get('/admin/schools/stats');
      setStats(res.data);
    } catch (err) {
      console.error('Failed to fetch stats', err);
    }
  };

  const toggleStatus = async (school) => {
    try {
      await api.put(`/admin/schools/${school.id}`, {
        is_active: !school.is_active,
      });
      fetchSchools();
      fetchStats();
    } catch (err) {
      console.error('Failed to update school', err);
    }
  };

  const deleteSchool = async (id) => {
    if (!confirm('Are you sure you want to delete this school?')) return;

    try {
      await api.delete(`/admin/schools/${id}`);
      fetchSchools();
      fetchStats();
    } catch (err) {
      console.error('Failed to delete school', err);
    }
  };

  return (
    <div>
      <div className="page-header">
        <h1>Schools</h1>
        <Link to="/super-admin/schools/create" className="btn btn-primary">+ Add School</Link>
      </div>

      <div className="stats-grid">
        <div className="stat-card">
          <div className="stat-value">{stats.total}</div>
          <div className="stat-label">Total Schools</div>
        </div>
        <div className="stat-card stat-active">
          <div className="stat-value">{stats.active}</div>
          <div className="stat-label">Active</div>
        </div>
        <div className="stat-card stat-inactive">
          <div className="stat-value">{stats.inactive}</div>
          <div className="stat-label">Inactive</div>
        </div>
      </div>

      <div className="table-card">
        <div className="table-header">
          <h2>All Schools</h2>
          <input
            type="text"
            placeholder="Search schools..."
            className="search-input"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>

        {loading ? (
          <div className="loading-state">Loading schools...</div>
        ) : schools.length === 0 ? (
          <div className="empty-state">No schools found. Add your first school.</div>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Subdomain</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {schools.map((school) => (
                <tr key={school.id}>
                  <td><strong>{school.name}</strong></td>
                  <td><code>{school.code}</code></td>
                  <td>{school.subdomain || '—'}</td>
                  <td>{school.email || '—'}</td>
                  <td>{school.plan?.name || '—'}</td>
                  <td>
                    <span className={`badge ${school.is_active ? 'badge-active' : 'badge-inactive'}`}>
                      {school.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td className="actions-cell">
                    <Link to={`/super-admin/schools/${school.id}/edit`} className="btn-sm btn-edit">Edit</Link>
                    <button className="btn-sm" onClick={() => toggleStatus(school)}>
                      {school.is_active ? 'Disable' : 'Enable'}
                    </button>
                    <Link to={`/super-admin/schools/${school.id}`} className="btn-sm btn-view">View</Link>
                    <button className="btn-sm btn-danger" onClick={() => deleteSchool(school.id)}>Delete</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
