export function getSubdomain() {
  let hostname = window.location.hostname;

  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return null;
  }

  const parts = hostname.split('.');

  if (parts.length >= 3) {
    return parts[0];
  }

  const match = hostname.match(/^([a-z0-9][a-z0-9-]*)\./i);
  return match ? match[1] : null;
}

export function isSchoolSubdomain() {
  const subdomain = getSubdomain();
  return subdomain !== null && subdomain !== 'www';
}
