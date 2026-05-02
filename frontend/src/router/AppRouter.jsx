import { BrowserRouter, Routes, Route } from 'react-router-dom';
import AdminLayout from '../layouts/AdminLayout';
import SchoolLayout from '../layouts/SchoolLayout';
import PortalLayout from '../layouts/PortalLayout';
import AdminDashboard from '../pages/admin/Dashboard';
import SchoolDashboard from '../pages/school/Dashboard';
import PortalDashboard from '../pages/portal/Dashboard';
import WebsiteHome from '../pages/website/Home';

function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<WebsiteHome />} />

        <Route path="/admin/*" element={<AdminLayout />}>
          <Route index element={<AdminDashboard />} />
        </Route>

        <Route path="/school/*" element={<SchoolLayout />}>
          <Route index element={<SchoolDashboard />} />
        </Route>

        <Route path="/portal/*" element={<PortalLayout />}>
          <Route index element={<PortalDashboard />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default AppRouter;
