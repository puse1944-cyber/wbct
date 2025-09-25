<?php
session_start();
error_reporting(0);
$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path . "/api/v1.1/core/brain.php";
include($path . "/static/v4/plugins/form/header.php");

// Verificar permisos de administrador
$query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
$query->bindParam("id", $_SESSION["user_id"], PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);
if (!$user || $user["suscripcion"] != 3) {
    header("Location: /");
    exit;
}

function alert($message, $success = false) {
    $icon = $success ? "success" : "error";
    echo "<script>Swal.fire({icon: '$icon', text: '$message', timer: 2000, showConfirmButton: false});</script>";
}

// Procesar formulario de nueva noticia
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author = $_POST['author'];
        $priority = intval($_POST['priority']);
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        
        try {
            $insert = $connection->prepare("INSERT INTO breathe_news (title, content, author, priority, is_published) VALUES (:title, :content, :author, :priority, :is_published)");
            $insert->bindParam("title", $title, PDO::PARAM_STR);
            $insert->bindParam("content", $content, PDO::PARAM_STR);
            $insert->bindParam("author", $author, PDO::PARAM_STR);
            $insert->bindParam("priority", $priority, PDO::PARAM_INT);
            $insert->bindParam("is_published", $is_published, PDO::PARAM_INT);
            
            if ($insert->execute()) {
                alert("Noticia creada exitosamente!", true);
            } else {
                alert("Error al crear la noticia.");
            }
        } catch (Exception $e) {
            alert("Error: " . $e->getMessage());
        }
    }
    
    if ($_POST['action'] === 'delete' && isset($_POST['news_id'])) {
        $news_id = intval($_POST['news_id']);
        try {
            $delete = $connection->prepare("DELETE FROM breathe_news WHERE id = :id");
            $delete->bindParam("id", $news_id, PDO::PARAM_INT);
            if ($delete->execute()) {
                alert("Noticia eliminada correctamente.", true);
            } else {
                alert("Error al eliminar la noticia.");
            }
        } catch (Exception $e) {
            alert("Error: " . $e->getMessage());
        }
    }
}

// Obtener todas las noticias
try {
    $query = $connection->query("SELECT * FROM breathe_news ORDER BY priority DESC, created_at DESC");
    $news = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $news = [];
    echo "<script>Swal.fire({icon: 'error', text: 'Error al cargar noticias: " . $e->getMessage() . "', timer: 3000});</script>";
}
?>

<section class="content">
    <header class="content__title">
        <h1><i class="zwicon-news"></i> Gestión de Noticias</h1>
    </header>

    <!-- Formulario para crear noticia -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Crear Nueva Noticia</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Contenido</label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Autor</label>
                                    <input type="text" name="author" class="form-control" value="<?php echo $user['username']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Prioridad</label>
                                    <select name="priority" class="form-control">
                                        <option value="0">Normal</option>
                                        <option value="1">Alta</option>
                                        <option value="2">Muy Alta</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_published" class="form-check-input" checked>
                                <label class="form-check-label">Publicar inmediatamente</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="zwicon-plus"></i> Crear Noticia
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Noticias:</span>
                            <span class="badge badge-primary"><?php echo count($news); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Publicadas:</span>
                            <span class="badge badge-success"><?php echo count(array_filter($news, function($n) { return $n['is_published'] == 1; })); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Borradores:</span>
                            <span class="badge badge-warning"><?php echo count(array_filter($news, function($n) { return $n['is_published'] == 0; })); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de noticias existentes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Noticias Existentes</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo substr($item['content'], 0, 100) . '...'; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td>
                                        <?php
                                        $priority_labels = [0 => 'Normal', 1 => 'Alta', 2 => 'Muy Alta'];
                                        $priority_colors = [0 => 'secondary', 1 => 'warning', 2 => 'danger'];
                                        $priority = $item['priority'];
                                        ?>
                                        <span class="badge badge-<?php echo $priority_colors[$priority]; ?>">
                                            <?php echo $priority_labels[$priority]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($item['is_published'] == 1): ?>
                                            <span class="badge badge-success">Publicada</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Borrador</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta noticia?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="news_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="zwicon-trash"></i>
                                            </button>
                                        </form>
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
</section>

<?php include($path . "/static/v4/plugins/form/footer.php"); ?>
