<!-- <div id="toast-message" class="toast-message">Successfully added!</div>
<div id="toast-update" class="toast-update">Successfully updated!</div>
<div id="toast-delete" class="toast-delete">Successfully deleted!</div> -->
<?php if (!empty($errors)) : ?>
    <?php foreach ($errors as $error) : ?>
        <div class="toast-validate"><?php echo $error; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['successfully_added']) && $_SESSION['successfully_added'] === true) { ?>
            showToast();
        <?php } ?>

        <?php if (isset($_SESSION['successfully_updated']) && $_SESSION['successfully_updated'] === true) { ?>
            showToastUpdate();
        <?php } ?>

        <?php if (isset($_SESSION['successfully_deleted']) && $_SESSION['successfully_deleted'] === true) { ?>
            showToastDelete();
        <?php } ?>

        function showToastUpdate() {
            let toast = document.getElementById('toast-update');
            toast.style.opacity = 1

            setTimeout(function() {
                toast.style.opacity = 0
            }, 3000)
        }

        function showToastDelete() {
            let toast = document.getElementById('toast-delete');
            toast.style.opacity = 1

            setTimeout(function() {
                toast.style.opacity = 0
            }, 3000)
        }

        function showToast() {
            let toast = document.getElementById('toast-message');
            toast.style.opacity = 1

            setTimeout(function() {
                toast.style.opacity = 0
            }, 3000)
        }
    });
</script>
<script src="https://kit.fontawesome.com/eac2918891.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script src="./../assets/js/index.js"></script>
</body>

</html>