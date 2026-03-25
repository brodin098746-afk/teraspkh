<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['role']!='admin'){
    die("Akses ditolak");
}

# RESET PASSWORD
if(isset($_GET['reset']) && isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $default_password = password_hash('12345678', PASSWORD_DEFAULT);

    $conn->query("UPDATE users SET password='$default_password' WHERE id='$id'");
    header("Location: user_list.php?status=success&msg=Password berhasil direset menjadi 12345678");
    exit;
}

# HAPUS USER
if(isset($_GET['hapus']) && isset($_GET['id'])){
    $id = (int)$_GET['id'];

    if($id != $_SESSION['user_id']){
        $conn->query("DELETE FROM users WHERE id='$id'");
        header("Location: user_list.php?status=success&msg=User berhasil dihapus");
    }else{
        header("Location: user_list.php?status=error&msg=Tidak bisa menghapus akun sendiri");
    }
    exit;
}

# HAPUS MULTIPLE USER
if(isset($_POST['hapus_selected'])){

    if(!empty($_POST['user_ids'])){

        foreach($_POST['user_ids'] as $uid){

            if($uid != $_SESSION['user_id']){
                $conn->query("DELETE FROM users WHERE id='$uid'");
            }

        }

        header("Location: user_list.php?status=success&msg=User berhasil dihapus");
        exit;

    }else{
        header("Location: user_list.php?status=error&msg=Pilih user terlebih dahulu");
        exit;
    }
}

# SEARCH & FILTER
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';

$where = "WHERE 1=1";

if($search != ''){
    $where .= " AND (nama LIKE '%$search%' OR username LIKE '%$search%')";
}

if($role != ''){
    $where .= " AND role='$role'";
}

$users = $conn->query("SELECT * FROM users $where ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manajemen User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
font-family:'Poppins',sans-serif;
background:#f4f6f9;
padding:20px;
}

.card{
border-radius:15px;
border:none;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

.card-header{
background:linear-gradient(135deg,#667eea,#764ba2);
color:white;
border-radius:15px 15px 0 0 !important;
}
</style>

</head>
<body>

<div class="container">

<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">
<i class="bi bi-people-fill me-2"></i>Manajemen User
</h5>

<div>
<a href="home.php" class="btn btn-sm btn-light me-2">
<i class="bi bi-house"></i> Home
</a>

<a href="user_tambah.php" class="btn btn-sm btn-success">
<i class="bi bi-plus-circle"></i> Tambah User
</a>
</div>

</div>

<div class="card-body">

<?php if(isset($_GET['status'])): ?>

<div class="alert alert-<?= $_GET['status']=='success'?'success':'danger' ?> alert-dismissible fade show">

<?= $_GET['msg'] ?>

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>

<!-- SEARCH & FILTER -->

<form method="GET" class="row mb-3">

<div class="col-md-4">
<input type="text"
name="search"
class="form-control"
placeholder="Cari nama / username"
value="<?= htmlspecialchars($search) ?>">
</div>

<div class="col-md-3">
<select name="role" class="form-select">

<option value="">Semua Role</option>

<option value="admin" <?= $role=='admin'?'selected':'' ?>>Admin</option>

<option value="user" <?= $role=='user'?'selected':'' ?>>User</option>

</select>
</div>

<div class="col-md-3">

<button class="btn btn-primary">
<i class="bi bi-search"></i> Cari
</button>

<a href="user_list.php" class="btn btn-secondary">
Reset
</a>
    
</div>
<button type="submit"
name="hapus_selected"
class="btn btn-danger mt-3"
onclick="return confirm('Hapus user yang dipilih?')">

<i class="bi bi-trash"></i> Hapus Yang Dipilih

</button>
</form>

<form method="POST">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="40">
<input type="checkbox" id="checkAll">
</th>

<th>No</th>
<th>Nama</th>
<th>Username</th>
<th>Role</th>
<th>Password Hash</th>
<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php
$no=1;
while($u=$users->fetch_assoc()):
?>

<tr>

<td>
<input type="checkbox" name="user_ids[]" value="<?= $u['id'] ?>">
</td>

<td><?= $no++ ?></td>

<td><?= htmlspecialchars($u['nama']) ?></td>

<td><?= htmlspecialchars($u['username']) ?></td>

<td>

<span class="badge bg-<?= $u['role']=='admin'?'danger':'success' ?>">
<?= $u['role'] ?>
</span>

</td>

<td>

<small class="text-muted">

<?= substr($u['password'],0,30) ?>...

</small>

</td>

<td>

<a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>

<a href="?reset&id=<?= $u['id'] ?>" class="btn btn-info btn-sm"
onclick="return confirm('Reset password menjadi 12345678?')">
<i class="bi bi-arrow-repeat"></i>
</a>

<?php if($u['id'] != $_SESSION['user_id']): ?>

<a href="?hapus&id=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Yakin ingin menghapus user ini?')">
<i class="bi bi-trash"></i>
</a>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<button type="submit"
name="hapus_selected"
class="btn btn-danger mt-3"
onclick="return confirm('Hapus user yang dipilih?')">

<i class="bi bi-trash"></i> Hapus Yang Dipilih

</button>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.getElementById('checkAll').addEventListener('click',function(){

let checkboxes=document.querySelectorAll('input[name="user_ids[]"]');

checkboxes.forEach(function(cb){

cb.checked=document.getElementById('checkAll').checked;

});

});

</script>

</body>
</html>
