<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || strtolower($_SESSION['usuario_rol']) !== 'administrador') {
    header('Location: index.php');
    exit;
}

include 'include/conect.php';

$mensaje = '';
$tipoMensaje = '';
$rolFiltro = $_GET['rol'] ?? '';
$fechaFiltro = $_GET['fecha'] ?? '';
$hayFiltro = ($rolFiltro !== '' || $fechaFiltro !== '');
$mensajeFiltro = '';
$mensajeResultados = '';
$textoVista = 'Todos los usuarios';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['id_rol'])) {
    $idUsuario = (int)$_POST['id_usuario'];
    $idRol = (int)$_POST['id_rol'];

    if ($idUsuario > 0 && in_array($idRol, [1, 2, 3], true)) {
        $stmt = $conexion->prepare('UPDATE usuarios SET id_rol = ? WHERE id_usuario = ?');
        $stmt->bind_param('ii', $idRol, $idUsuario);
        if ($stmt->execute()) {
            $mensaje = 'Rol actualizado correctamente.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'No se pudo actualizar el rol.';
            $tipoMensaje = 'danger';
        }
        $stmt->close();
    } else {
        $mensaje = 'Datos inválidos para actualizar el rol.';
        $tipoMensaje = 'danger';
    }
}

$sql = 'SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.telefono, u.fecha_registro, u.id_rol, r.nombre_rol
        FROM usuarios u
        INNER JOIN rol r ON u.id_rol = r.id_rol WHERE 1=1';
$params = [];
$types = '';

if ($rolFiltro !== '') {
    $sql .= ' AND u.id_rol = ?';
    $params[] = (int)$rolFiltro;
    $types .= 'i';
}
if ($fechaFiltro !== '') {
    $sql .= ' AND u.fecha_registro = ?';
    $params[] = $fechaFiltro;
    $types .= 's';
}

$sql .= ' ORDER BY u.id_usuario ASC';
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
$usuarios = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
$conexion->close();

if ($hayFiltro) {
    $mensajeFiltro = 'Filtro aplicado con éxito.';
    $mensajeResultados = 'Se encontraron ' . count($usuarios) . ' resultado(s).';

    if ($rolFiltro !== '' && $fechaFiltro !== '') {
        $nombreRol = '';
        if ((int)$rolFiltro === 1) {
            $nombreRol = 'clientes';
        } elseif ((int)$rolFiltro === 2) {
            $nombreRol = 'editores';
        } elseif ((int)$rolFiltro === 3) {
            $nombreRol = 'administradores';
        }
        $textoVista = 'Todos los ' . $nombreRol . ' del ' . date('d/m/Y', strtotime($fechaFiltro));
    } elseif ($rolFiltro !== '') {
        if ((int)$rolFiltro === 1) {
            $textoVista = 'Todos los clientes';
        } elseif ((int)$rolFiltro === 2) {
            $textoVista = 'Todos los editores';
        } elseif ((int)$rolFiltro === 3) {
            $textoVista = 'Todos los administradores';
        }
    } elseif ($fechaFiltro !== '') {
        $textoVista = 'Usuarios registrados el ' . date('d/m/Y', strtotime($fechaFiltro));
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Gestión de usuarios de la tienda de juguetes." />
  <title>Gestión de usuarios</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
  <?php include 'views/navbar.php'; ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
              <div>
                <h2 class="fw-bold mb-1">Gestión de usuarios</h2>
                <p class="text-muted mb-0">Administra los usuarios, asigna roles y controla cuentas desde un solo panel.</p>
              </div>
              <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al inicio
              </a>
            </div>

            <?php if ($mensaje !== ''): ?>
              <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?>" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
              </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
              <div class="col-12 col-lg-4">
                <div class="border rounded-4 p-3 bg-light">
                  <h5 class="fw-bold mb-3"><i class="bi bi-person-plus-fill me-2"></i>Crear usuario</h5>
                  <form id="createUserForm" novalidate>
                    <div class="mb-2">
                      <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Nombre" required>
                    </div>
                    <div class="mb-2">
                      <input type="text" name="apellido" class="form-control form-control-sm" placeholder="Apellido" required>
                    </div>
                    <div class="mb-2">
                      <input type="email" name="correo" class="form-control form-control-sm" placeholder="Correo" required>
                    </div>
                    <div class="mb-2">
                      <input type="tel" name="telefono" class="form-control form-control-sm" placeholder="Teléfono">
                    </div>
                    <div class="mb-2">
                      <input type="password" name="contrasena" class="form-control form-control-sm" placeholder="Contraseña" required>
                    </div>
                    <div class="mb-2">
                      <select name="sexo" class="form-select form-select-sm">
                        <option value="">Sexo</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <select name="id_rol" class="form-select form-select-sm">
                        <option value="1">Cliente</option>
                        <option value="2">Editor</option>
                        <option value="3">Administrador</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Crear usuario</button>
                  </form>
                </div>
              </div>
              <div class="col-12 col-lg-8">
                <div class="border rounded-4 p-3 bg-light">
                  <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill me-2"></i>Filtros</h5>
                  <form method="get" id="filterForm" class="row g-2">
                    <div class="col-12 col-md-6">
                      <select name="rol" class="form-select form-select-sm">
                        <option value="">Todos los roles</option>
                        <option value="1" <?php echo $rolFiltro === '1' ? 'selected' : ''; ?>>Cliente</option>
                        <option value="2" <?php echo $rolFiltro === '2' ? 'selected' : ''; ?>>Editor</option>
                        <option value="3" <?php echo $rolFiltro === '3' ? 'selected' : ''; ?>>Administrador</option>
                      </select>
                    </div>
                    <div class="col-12 col-md-6">
                      <input type="date" name="fecha" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                    </div>
                    <div class="col-12 text-end">
                      <button class="btn btn-sm btn-outline-secondary" type="submit">Aplicar</button>
                      <a class="btn btn-sm btn-outline-dark" href="gestion.php#resultados">Limpiar</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <?php if ($mensajeFiltro !== ''): ?>
              <div class="alert alert-success mb-3" role="status">
                <div class="fw-semibold"><?php echo htmlspecialchars($mensajeFiltro); ?></div>
                <div class="small mt-1"><?php echo htmlspecialchars($mensajeResultados); ?></div>
              </div>
            <?php endif; ?>

            <div id="resultados" class="table-responsive">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <div class="fw-semibold text-dark"><?php echo htmlspecialchars($textoVista); ?></div>
                  <div class="text-muted small">Resultados</div>
                </div>
                <?php if ($hayFiltro): ?>
                  <span class="text-success"><?php echo count($usuarios); ?> encontrados</span>
                <?php endif; ?>
              </div>
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Fecha registro</th>
                    <th>Rol actual</th>
                    <th class="text-end">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                      <td><?php echo (int)$usuario['id_usuario']; ?></td>
                      <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                      <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                      <td><?php echo htmlspecialchars($usuario['telefono'] ?: '—'); ?></td>
                      <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                      <td>
                        <span class="badge text-capitalize" style="background-color: var(--purple); color: #fff;">
                          <?php echo htmlspecialchars($usuario['nombre_rol']); ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                          <form method="post" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="id_usuario" value="<?php echo (int)$usuario['id_usuario']; ?>">
                            <select name="id_rol" class="form-select form-select-sm" style="max-width: 140px;">
                              <option value="1" <?php echo (int)$usuario['id_rol'] === 1 ? 'selected' : ''; ?>>Cliente</option>
                              <option value="2" <?php echo (int)$usuario['id_rol'] === 2 ? 'selected' : ''; ?>>Editor</option>
                              <option value="3" <?php echo (int)$usuario['id_rol'] === 3 ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                          </form>
                          <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn" data-user-id="<?php echo (int)$usuario['id_usuario']; ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const createForm = document.getElementById('createUserForm');
      if (createForm) {
        createForm.addEventListener('submit', async function (event) {
          event.preventDefault();
          const formData = new FormData(createForm);
          formData.append('action', 'create');

          const btn = createForm.querySelector('button[type="submit"]');
          const originalText = btn.innerHTML;
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';

          try {
            const response = await fetch('include/admin_user_management.php', {
              method: 'POST',
              body: formData
            });
            const data = await response.json();
            alert(data.message);
            if (data.success) {
              window.location.reload();
            }
          } catch (error) {
            alert('No se pudo completar la acción.');
          } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
          }
        });
      }

      const resultsSection = document.getElementById('resultados');
      const hasActiveFilter = window.location.search.includes('rol=') || window.location.search.includes('fecha=');

      if (hasActiveFilter && resultsSection) {
        setTimeout(function () {
          resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
      }

      document.querySelectorAll('.delete-user-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
          const userId = this.dataset.userId;
          const confirmed = confirm('¿Seguro que deseas eliminar este usuario?');
          if (!confirmed) return;

          const formData = new FormData();
          formData.append('action', 'delete');
          formData.append('id_usuario', userId);

          try {
            const response = await fetch('include/admin_user_management.php', {
              method: 'POST',
              body: formData
            });
            const data = await response.json();
            alert(data.message);
            if (data.success) {
              window.location.reload();
            }
          } catch (error) {
            alert('No se pudo eliminar el usuario.');
          }
        });
      });
    });
  </script>
</body>
</html>
