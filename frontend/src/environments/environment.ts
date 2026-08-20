export const environment = {
  production: false,
  // DESARROLLO: base vacía a propósito. Las peticiones salen como
  // /controllers/Xxx.php y el proxy (proxy.conf.json) las reenvía al
  // Apache de XAMPP donde vive el backend PHP. Como el navegador ve un
  // solo origen (localhost:4200), la cookie de sesión de PHP viaja sin
  // problemas de CORS. En producción, ver environment.prod.ts.
  phpApiUrl: ''
};
