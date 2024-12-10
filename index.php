<?php
require('./class/Libros.php');
session_start();

// Inicializar la sesión de libros si no está definida
if (!isset($_SESSION['Libros'])) {
    $_SESSION['Libros'] = [];
}

$Libros = &$_SESSION['Libros']; // Referencia para evitar duplicación

// Redirigir a la página principal
function redirigirPrincipal() {
    header('Location: /biblioteca/');
    exit;
}

function getLibroPorID($id, $Libros) {
    foreach ($Libros as $libro) {
        if ($libro->getId() == $id) {
            return $libro;
        }
    }
    return null; // Devuelve null si no se encuentra el libro
}


// Crear un libro
if (isset($_POST['createForm'])) {
    if (!empty($_POST['tituloFrm']) && !empty($_POST['autorFrm']) && !empty($_POST['categoriaFrm'])) {
        $id = count($Libros) + 1;
        $libro = new Libro($id, $_POST['tituloFrm'], $_POST['autorFrm'], $_POST['categoriaFrm']);
        $Libros[] = $libro;
        redirigirPrincipal();
    }
}

// Actualizar un libro
if (isset($_POST['updateForm'], $_POST['id'])) {
    foreach ($Libros as $libro) {
        if ($libro->getId() == $_POST['id']) {
            $libro->setTitulo($_POST['tituloFrm'] ?? $libro->getTitulo());
            $libro->setAutor($_POST['autorFrm'] ?? $libro->getAutor());
            $libro->setCategoria($_POST['categoriaFrm'] ?? $libro->getCategoria());
            break;
        }
    }
    redirigirPrincipal();
}

// Eliminar un libro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $Libros = array_filter($Libros, fn($libro) => $libro->getId() != $id);
    redirigirPrincipal();
}

// Gestionar préstamo o devolución
function actualizarDisponibilidad($id, $disponible) {
    global $Libros;
    foreach ($Libros as $libro) {
        if ($libro->getId() == $id) {
            $libro->setDisponible($disponible);
            break;
        }
    }
}

if (isset($_GET['prestamo'])) {
    actualizarDisponibilidad($_GET['prestamo'], false);
    redirigirPrincipal();
}

if (isset($_GET['devolver'])) {
    actualizarDisponibilidad($_GET['devolver'], true);
    redirigirPrincipal();
}

// Buscar libros
$LibrosFiltrados = $Libros;
if (isset($_GET['buscarPor'], $_GET['terminoBusqueda'])) {
    $criterio = $_GET['buscarPor'];
    $termino = strtolower($_GET['terminoBusqueda']);

    $LibrosFiltrados = array_filter($Libros, function ($libro) use ($criterio, $termino) {
        return match ($criterio) {
            'titulo' => stripos($libro->getTitulo(), $termino) !== false,
            'autor' => stripos($libro->getAutor(), $termino) !== false,
            'categoria' => stripos($libro->getCategoria(), $termino) !== false,
            default => false,
        };
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Administracion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-800 text-white font-sans">
    <h1 class="text-3xl font-bold text-center my-6">Sistema de Biblioteca</h1>

    <form method="GET" action="" class="flex justify-center items-center space-x-4 mb-6">
        <label for="buscarPor" class="text-lg">Buscar por:</label>
        <select name="buscarPor" id="buscarPor" class="p-2 rounded bg-gray-700 text-white">
            <option value="titulo" <?php echo isset($_GET['buscarPor']) && $_GET['buscarPor'] == 'titulo' ? 'selected' : ''; ?>>Título</option>
            <option value="autor" <?php echo isset($_GET['buscarPor']) && $_GET['buscarPor'] == 'autor' ? 'selected' : ''; ?>>Autor</option>
            <option value="categoria" <?php echo isset($_GET['buscarPor']) && $_GET['buscarPor'] == 'categoria' ? 'selected' : ''; ?>>Categoría</option>
        </select>

        <input type="text" name="terminoBusqueda" value="<?php echo isset($_GET['terminoBusqueda']) ? $_GET['terminoBusqueda'] : ''; ?>" placeholder="Ingrese el término de búsqueda" class="p-2 rounded bg-gray-700 text-white">
        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white p-2 rounded">Buscar</button>
    </form>

    <?php if (isset($_GET['edit'])) {
        $libroEditable = getLibroPorID($_GET['edit'], $Libros);
    ?>
        <form method="POST" action="" class="max-w-md mx-auto space-y-4">
            <input type="hidden" name="updateForm">
            <input type="hidden" name="id" value="<?php echo $libroEditable->getId() ?>">

            <div>
                <label class="block text-lg">Título del libro</label>
                <input type="text" name="tituloFrm" value="<?php echo $libroEditable->getTitulo() ?>" class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <div>
                <label class="block text-lg">Autor del libro</label>
                <input type="text" name="autorFrm" value="<?php echo $libroEditable->getAutor() ?>" class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <div>
                <label class="block text-lg">Categoría</label>
                <input type="text" name="categoriaFrm" value="<?php echo $libroEditable->getCategoria() ?>" class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white p-2 rounded">Editar Libro</button>
        </form>
    <?php } else { ?>
        <form method="POST" action="" class="max-w-md mx-auto space-y-4">
            <input type="hidden" name="createForm" value="soy el create">

            <div>
                <label class="block text-lg">Título del Libro</label>
                <input type="text" name="tituloFrm" required class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <div>
                <label class="block text-lg">Autor del Libro</label>
                <input type="text" name="autorFrm" required class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <div>
                <label class="block text-lg">Categoría</label>
                <select name="categoriaFrm" required class="w-full p-2 rounded bg-gray-700 text-white">
                    <option value="Ficcion">Ficción</option>
                    <option value="MisterioSuspenso">Misterio Suspenso</option>
                    <option value="CienciaFiccion">Ciencia Ficción</option>
                    <option value="Romantico">Romántico</option>
                    <option value="Autoayuda">Autoayuda</option>
                    <option value="BiografiaMemorias">Biografía Memorias</option>
                    <option value="Historia">Historia</option>
                    <option value="Otra">Otra</option>
                </select>
            </div>

            <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white p-2 rounded">Registrar libro</button>
        </form>
    <?php } ?>

    <main class="max-w-5xl mx-auto">
        <table class="w-full border border-gray-600 text-center mt-6">
            <thead class="bg-gray-700">
                <tr>
                    <th class="p-2 border border-gray-600">ID</th>
                    <th class="p-2 border border-gray-600">Título</th>
                    <th class="p-2 border border-gray-600">Autor</th>
                    <th class="p-2 border border-gray-600">Categoría</th>
                    <th class="p-2 border border-gray-600">Disponible</th>
                    <th class="p-2 border border-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($LibrosFiltrados as $libro): ?>
                    <tr class="odd:bg-gray-700 even:bg-gray-600">
                        <td class="p-2 border border-gray-600"><?php echo $libro->getId(); ?></td>
                        <td class="p-2 border border-gray-600"><?php echo $libro->getTitulo(); ?></td>
                        <td class="p-2 border border-gray-600"><?php echo $libro->getAutor(); ?></td>
                        <td class="p-2 border border-gray-600"><?php echo $libro->getCategoria(); ?></td>
                        <td class="p-2 border border-gray-600"><?php echo $libro->getDisponible() ? 'Sí' : 'No'; ?></td>
                        <td class="p-2 border border-gray-600 space-x-2">
                            <a href="?edit=<?php echo $libro->getId(); ?>" class="text-teal-400 hover:text-teal-300 hover:bg-[#1f2937] p-2 rounded transition-all">Editar</a>
                            <a href="?delete=<?php echo $libro->getId(); ?>" class="text-red-400 hover:text-red-300 hover:bg-[#1f2937] p-2 rounded transition-all">Eliminar</a>
                            <a href="?prestamo=<?php echo $libro->getId(); ?>" class="text-yellow-400 hover:text-yellow-300 hover:bg-[#1f2937] p-2 rounded transition-all">Solicitar Préstamo</a>

                            <?php if (!$libro->getDisponible()): ?>
                                <a href="?devolver=<?php echo $libro->getId(); ?>" class="text-blue-400 hover:text-blue-300 hover:bg-[#1f2937] p-2 rounded transition-all">Devolver</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
