import { useState, useEffect } from 'react';
import api from '../../config/api';

export default function Users() {
  const [users, setUsers] = useState([]);
  const [stats, setStats] = useState({ total: 0, by_role: {} });
  const [schools, setSchools] = useState([]);
  const [roles] = useState(['super_admin', 'school_admin', 'teacher', 'student', 'parent']);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState(null);
  const [search, setSearch] = useState('');
  const [filterRole, setFilterRole] = useState('');
  const [formData, setFormData] = useState({ name: '', email: '', phone: '', school_id: '', password: '', roles: [] });
  const [error, setError] = useState('');

  useEffect(() => { fetchData(); fetchStats(); }, [search, filterRole]);
  useEffect(() => { api.get('/admin/schools').then(r => setSchools(r.data.schools?.data || [])).catch(() => {}); }, []);

  const fetchData = async () => {
    try {
      const params = {};
      if (search) params.search = search;
      if (filterRole) params.role = filterRole;
      const res = await api.get('/admin/users', { params });
      setUsers(res.data.users?.data || []);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const fetchStats = async () => {
    try {
      const res = await api.get('/admin/users/stats');
      setStats(res.data);
    } catch (err) { console.error(err); }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    try {
      const data = {
        ...formData,
        school_id: formData.school_id || null,
        roles: formData.roles.length > 0 ? formData.roles : ['student'],
      };
      if (editing) {
        await api.put(`/admin/users/${editing.id}`, data);
      } else {
        await api.post('/admin/users', data);
      }
      resetForm();
      fetchData();
      fetchStats();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed');
    }
  };

  const handleEdit = (user) => {
    setEditing(user);
    setFormData({
      name: user.name, email: user.email, phone: user.phone || '',
      school_id: user.school_id || '', password: '',
      roles: user.roles?.map(r => r.name) || [],
    });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (!confirm('Delete this user?')) return;
    await api.delete(`/admin/users/${id}`);
    fetchData();
    fetchStats();
  };

  const resetForm = () => {
    setShowForm(false);
    setEditing(null);
    setFormData({ name: '', email: '', phone: '', school_id: '', password: '', roles: [] });
  };

  const toggleRole = (role) => {
    const roles = formData.roles.includes(role)
      ? formData.roles.filter(r => r !== role)
      : [...formData.roles, role];
    setFormData({ ...formData, roles });
  };

  const roleLabels = { super_admin: 'Super Admin', school_admin: 'School Admin', teacher: 'Teacher', student: 'Student', parent: 'Parent' };

  return (
    <div>
      <div className="page-header">
        <h1>Users</h1>
        <button className="btn btn-primary" onClick={() => setShowForm(true)}>+ Add User</button>
      </div>

      <div className="stats-grid">
        <div className="stat-card"><div className="stat-value">{stats.total}</div><div className="stat-label">Total Users</div></div>
        {roles.map(role => (
          <div key={role} className="stat-card">
            <div className="stat-value">{stats.by_role?.[role] || 0}</div>
            <div className="stat-label">{roleLabels[role]}</div>
          </div>
        ))}
      </div>

      {showForm && (
        <div className="form-card">
          <h3>{editing ? 'Edit User' : 'New User'}</h3>
          {error && <div className="error-message">{error}</div>}
          <form onSubmit={handleSubmit}>
            <div className="form-grid">
              <div className="form-group"><label>Name *</label>
                <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} required /></div>
              <div className="form-group"><label>Email *</label>
                <input type="email" value={formData.email} onChange={(e) => setFormData({ ...formData, email: e.target.value })} required /></div>
              <div className="form-group"><label>Phone</label>
                <input type="text" value={formData.phone} onChange={(e) => setFormData({ ...formData, phone: e.target.value })} /></div>
              <div className="form-group"><label>School</label>
                <select value={formData.school_id} onChange={(e) => setFormData({ ...formData, school_id: e.target.value })}>
                  <option value="">No School</option>
                  {schools.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                </select></div>
              <div className="form-group"><label>Password {!editing ? '*' : '(leave blank to keep)'}</label>
                <input type="password" value={formData.password} onChange={(e) => setFormData({ ...formData, password: e.target.value })} required={!editing} /></div>
              <div className="form-group full-width">
                <label>Roles</label>
                <div className="role-checks">
                  {roles.map(role => (
                    <label key={role} className="check-item">
                      <input type="checkbox" checked={formData.roles.includes(role)} onChange={() => toggleRole(role)} />
                      {roleLabels[role]}
                    </label>
                  ))}
                </div>
              </div>
            </div>
            <div className="form-actions">
              <button type="button" className="btn btn-secondary" onClick={resetForm}>Cancel</button>
              <button type="submit" className="btn btn-primary">{editing ? 'Update' : 'Create'}</button>
            </div>
          </form>
        </div>
      )}

      <div className="table-card">
        <div className="table-header">
          <h2>All Users</h2>
          <div className="filter-group">
            <select value={filterRole} onChange={(e) => setFilterRole(e.target.value)} className="filter-select">
              <option value="">All Roles</option>
              {roles.map(r => <option key={r} value={r}>{roleLabels[r]}</option>)}
            </select>
            <input type="text" placeholder="Search..." className="search-input" value={search} onChange={(e) => setSearch(e.target.value)} />
          </div>
        </div>

        {loading ? <div className="loading-state">Loading...</div> : (
          <table className="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>School</th><th>Roles</th><th>Actions</th></tr></thead>
            <tbody>
              {users.map(user => (
                <tr key={user.id}>
                  <td><strong>{user.name}</strong></td>
                  <td>{user.email}</td>
                  <td>{user.phone || '—'}</td>
                  <td>{user.school?.name || '—'}</td>
                  <td>
                    {user.roles?.map(r => <span key={r.id} className="badge badge-active">{roleLabels[r.name] || r.name}</span>)}
                  </td>
                  <td className="actions-cell">
                    <button className="btn-sm btn-edit" onClick={() => handleEdit(user)}>Edit</button>
                    <button className="btn-sm btn-danger" onClick={() => handleDelete(user.id)}>Delete</button>
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
