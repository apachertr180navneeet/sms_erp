import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import api from '../../config/api';

export default function SchoolDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [school, setSchool] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchSchool();
  }, [id]);

  const fetchSchool = async () => {
    try {
      const res = await api.get(`/admin/schools/${id}`);
      setSchool(res.data.school);
    } catch (err) {
      setError('Failed to load school details');
    } finally {
      setLoading(false);
    }
  };

  const toggleStatus = async () => {
    try {
      await api.put(`/admin/schools/${id}`, {
        is_active: !school.is_active,
      });
      fetchSchool();
    } catch (err) {
      console.error('Failed to update school', err);
    }
  };

  const deleteSchool = async () => {
    if (!confirm('Are you sure you want to delete this school?')) return;

    try {
      await api.delete(`/admin/schools/${id}`);
      navigate('/super-admin/schools');
    } catch (err) {
      console.error('Failed to delete school', err);
    }
  };

  if (loading) return <div className="loading-state">Loading...</div>;
  if (error) return <div className="error-message">{error}</div>;
  if (!school) return <div className="empty-state">School not found</div>;

  return (
    <div>
      <div className="page-header">
        <h1>{school.name}</h1>
        <div className="header-actions">
          <Link to="/super-admin/schools" className="btn btn-secondary">Back</Link>
          <Link to={`/super-admin/schools/${school.id}/edit`} className="btn btn-primary">Edit</Link>
          <button className="btn btn-warning" onClick={toggleStatus}>
            {school.is_active ? 'Disable' : 'Enable'}
          </button>
          <button className="btn btn-danger" onClick={deleteSchool}>Delete</button>
        </div>
      </div>

      <div className="detail-grid">
        <div className="detail-card">
          <h3>School Information</h3>
          <div className="detail-row">
            <span className="detail-label">Name</span>
            <span className="detail-value">{school.name}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Code</span>
            <span className="detail-value"><code>{school.code}</code></span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Slug</span>
            <span className="detail-value">{school.slug}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Subdomain</span>
            <span className="detail-value">{school.subdomain || '—'}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Email</span>
            <span className="detail-value">{school.email || '—'}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Phone</span>
            <span className="detail-value">{school.phone || '—'}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Address</span>
            <span className="detail-value">{school.address || '—'}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Website URL</span>
            <span className="detail-value">
              {school.url ? (
                <a href={school.url} target="_blank" rel="noopener noreferrer">{school.url}</a>
              ) : (
                school.website_url || '—'
              )}
            </span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Subdomain URL</span>
            <span className="detail-value">
              {school.subdomain ? (
                <a href={`https://${school.subdomain}.localhost`} target="_blank" rel="noopener noreferrer">
                  https://{school.subdomain}.localhost
                </a>
              ) : '—'}
            </span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Plan</span>
            <span className="detail-value">{school.plan?.name || '—'}</span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Status</span>
            <span className="detail-value">
              <span className={`badge ${school.is_active ? 'badge-active' : 'badge-inactive'}`}>
                {school.is_active ? 'Active' : 'Inactive'}
              </span>
            </span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Created</span>
            <span className="detail-value">{new Date(school.created_at).toLocaleDateString()}</span>
          </div>
        </div>

        <div className="detail-card">
          <h3>Users ({school.users?.length || 0})</h3>
          {school.users?.length === 0 ? (
            <div className="empty-state">No users assigned to this school.</div>
          ) : (
            <table className="data-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Roles</th>
                </tr>
              </thead>
              <tbody>
                {school.users?.map(user => (
                  <tr key={user.id}>
                    <td>{user.name}</td>
                    <td>{user.email}</td>
                    <td>
                      {user.roles?.map(r => (
                        <span key={r.id} className="badge badge-active">{r.name}</span>
                      ))}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>
    </div>
  );
}
