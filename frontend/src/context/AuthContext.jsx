import { createContext, useContext, useState, useEffect } from 'react';
import api from '../config/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      api.get('/auth/user')
        .then(res => setUser(res.data.user))
        .catch(() => {
          localStorage.removeItem('token');
          setUser(null);
        })
        .finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, []);

  const login = async (email, password) => {
    const res = await api.post('/auth/login', { email, password });
    localStorage.setItem('token', res.data.token);
    setUser(res.data.user);
    return res.data;
  };

  const register = async (email, password, name) => {
    const res = await api.post('/auth/register', { email, password, password_confirmation: password, name });
    localStorage.setItem('token', res.data.token);
    setUser(res.data.user);
    return res.data;
  };

  const registerSchool = async (data) => {
    const res = await api.post('/schools/register', {
      school_name: data.schoolName,
      school_email: data.schoolEmail,
      school_phone: data.schoolPhone,
      admin_name: data.adminName,
      admin_email: data.adminEmail,
      admin_password: data.adminPassword,
      admin_password_confirmation: data.adminPasswordConfirmation,
    });
    localStorage.setItem('token', res.data.token);
    setUser(res.data.admin);
    return res.data;
  };

  const logout = async () => {
    try {
      await api.post('/auth/logout');
    } finally {
      localStorage.removeItem('token');
      setUser(null);
    }
  };

  const hasRole = (role) => {
    return user?.roles?.some(r => r.name === role);
  };

  const getRedirectPath = () => {
    if (hasRole('super_admin')) return '/super-admin';
    if (hasRole('school_admin')) return '/school-admin';
    if (hasRole('teacher')) return '/portal';
    if (hasRole('student')) return '/portal';
    if (hasRole('parent')) return '/portal';
    return '/';
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, register, registerSchool, logout, hasRole, getRedirectPath }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
