import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from '../context/AuthContext';
import ProtectedRoute from './ProtectedRoute';
import AdminLayout from '../layouts/AdminLayout';
import SchoolLayout from '../layouts/SchoolLayout';
import PortalLayout from '../layouts/PortalLayout';
import AdminDashboard from '../pages/admin/Dashboard';
import Schools from '../pages/super-admin/Schools';
import SchoolDetail from '../pages/super-admin/SchoolDetail';
import EditSchool from '../pages/super-admin/EditSchool';
import CreateSchool from '../pages/super-admin/CreateSchool';
import SchoolSubscription from '../pages/super-admin/SchoolSubscription';
import Modules from '../pages/super-admin/Modules';
import Plans from '../pages/super-admin/Plans';
import Users from '../pages/super-admin/Users';
import SchoolDashboard from '../pages/school/Dashboard';
import PortalDashboard from '../pages/portal/Dashboard';
import WebsiteHome from '../pages/website/Home';
import SuperAdminLogin from '../pages/auth/SuperAdminLogin';
import SchoolAdminLogin from '../pages/auth/SchoolAdminLogin';
import Register from '../pages/auth/Register';
import RegisterSchool from '../pages/auth/RegisterSchool';
import Unauthorized from '../pages/Unauthorized';
import { getSubdomain } from '../config/subdomain';

function AuthRedirect() {
  const { user, loading, getRedirectPath } = useAuth();

  if (loading) return <div className="loading">Loading...</div>;
  if (user) return <Navigate to={getRedirectPath()} replace />;
  return <Navigate to="/super-admin/login" replace />;
}

function SubdomainRoute({ children }) {
  const subdomain = getSubdomain();
  if (!subdomain) {
    return <Navigate to="/school-admin/login" replace />;
  }
  return children;
}

function AppRouter() {
  const isSubdomain = getSubdomain() !== null;

  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<WebsiteHome />} />

          <Route path="/super-admin/login" element={<SuperAdminLogin />} />
          <Route path="/school-admin/login" element={<SchoolAdminLogin />} />
          <Route path="/login" element={<SchoolAdminLogin />} />
          <Route path="/register" element={<Register />} />
          <Route path="/register-school" element={<RegisterSchool />} />
          <Route path="/unauthorized" element={<Unauthorized />} />
          <Route path="/redirect" element={<AuthRedirect />} />

          <Route path="/super-admin/*" element={
            <ProtectedRoute roles={['super_admin']}>
              <AdminLayout />
            </ProtectedRoute>
          }>
            <Route index element={<AdminDashboard />} />
            <Route path="schools" element={<Schools />} />
            <Route path="schools/:id" element={<SchoolDetail />} />
            <Route path="schools/:id/edit" element={<EditSchool />} />
            <Route path="schools/:id/subscription" element={<SchoolSubscription />} />
            <Route path="schools/create" element={<CreateSchool />} />
            <Route path="modules" element={<Modules />} />
            <Route path="plans" element={<Plans />} />
            <Route path="users" element={<Users />} />
          </Route>

          {isSubdomain ? (
            <Route path="/*" element={
              <ProtectedRoute roles={['school_admin', 'teacher', 'student', 'parent']}>
                <SchoolLayout />
              </ProtectedRoute>
            }>
              <Route index element={<SchoolDashboard />} />
            </Route>
          ) : (
            <Route path="/school-admin/*" element={
              <ProtectedRoute roles={['school_admin', 'teacher']}>
                <SchoolLayout />
              </ProtectedRoute>
            }>
              <Route index element={<SchoolDashboard />} />
            </Route>
          )}

          <Route path="/portal/*" element={
            <ProtectedRoute roles={['student', 'parent', 'teacher']}>
              <PortalLayout />
            </ProtectedRoute>
          }>
            <Route index element={<PortalDashboard />} />
          </Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default AppRouter;
