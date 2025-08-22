<?php
$title = 'Edit Roster';

include __DIR__ . '/../views/layouts/header.php';

if (!isset($_SESSION['userID'])) {
    redirect('./login');
    exit();
} else {
    $cycle = $_GET['cycle'];
    $roster_id = $_GET['roster_id'];

    require_once './controllers/Rosters.php';
    $init = new Rosters;

    $roster = $init->getRostersByID($roster_id);
}

?>

<main id="edit-roster">
    <div class="form-container">
        <h1>Edit Roster</h1>

        <?php flash('edit-roster-error'); ?>

        <form action="./controllers/Rosters.php" method="POST">

            <input type="hidden" name="action" value="editRoster">

            <?php if (isset($cycle)) : ?>
            <input type="hidden" name="cycle" value="<?php echo $cycle; ?>">
            <?php endif; ?>

            <?php if (isset($roster_id)) : ?>
                <input type="hidden" name="roster_id" value="<?php echo $roster_id; ?>">
            <?php endif; ?>

            <div class="form-group">
                <input type="date" class="form-input" id="date" name="date" value="<?php echo htmlspecialchars($roster->date); ?>" readonly disabled>
                <label for="date" class="form-label">Date</label>
            </div>

            <div class="form-group">
                <select class="form-input" id="type" name="type" required>
                    <option value="" disabled <?php echo !$roster ? 'selected' : ''; ?>>Select Type</option>
                    <option value="0700-1636" <?php echo $roster && $roster->type === '0700-1636' ? 'selected' : ''; ?>>0700-1636</option>
                    <option value="1500-0036" <?php echo $roster && $roster->type === '1500-0036' ? 'selected' : ''; ?>>1500-0036</option>
                    <option value="2300-0836" <?php echo $roster && $roster->type === '2300-0836' ? 'selected' : ''; ?>>2300-0836</option>
                    <option value="RDO" <?php echo $roster && $roster->type === 'RDO' ? 'selected' : ''; ?>>RDO</option>
                    <option value="AL" <?php echo $roster && $roster->type === 'AL' ? 'selected' : ''; ?>>AL</option>
                    <option value="SickLeave" <?php echo $roster && $roster->type === 'SickLeave' ? 'selected' : ''; ?>>SickLeave</option>
                    <option value="EPHO" <?php echo $roster && $roster->type === 'EPHO' ? 'selected' : ''; ?>>EPHO</option>
                    <option value="SPHO" <?php echo $roster && $roster->type === 'SPHO' ? 'selected' : ''; ?>>SPHO</option>
                    <option value="PHO" <?php echo $roster && $roster->type === 'PHO' ? 'selected' : ''; ?>>PHO</option>
                    <option value="FCL" <?php echo $roster && $roster->type === 'FCL' ? 'selected' : ''; ?>>FCL</option>
                    <option value="SL" <?php echo $roster && $roster->type === 'SL' ? 'selected' : ''; ?>>SL</option>
                    <option value="OL" <?php echo $roster && $roster->type === 'OL' ? 'selected' : ''; ?>>OL</option>
                    <option value="TL" <?php echo $roster && $roster->type === 'TL' ? 'selected' : ''; ?>>TL</option>
                    <option value="Morning6Hrs" <?php echo $roster && $roster->type === 'Morning6Hrs' ? 'selected' : ''; ?>>Morning6Hrs</option>
                    <option value="Day6Hrs" <?php echo $roster && $roster->type === 'Day6Hrs' ? 'selected' : ''; ?>>Day6Hrs</option>
                    <option value="Night6Hrs" <?php echo $roster && $roster->type === 'Night6Hrs' ? 'selected' : ''; ?>>Night6Hrs</option>
                    <option value="LWOP" <?php echo $roster && $roster->type === 'LWOP' ? 'selected' : ''; ?>>LWOP</option>
                    <option value="CasinoCloseNoRDO" <?php echo $roster && $roster->type === 'CasinoCloseNoRDO' ? 'selected' : ''; ?>>CasinoCloseNoRDO</option>
                </select>
                <label for="type" class="form-label">Type</label>
            </div>

            <div class="form-group">
                <input type="text" class="form-input" id="remark" name="remark" value="<?php echo $roster ? htmlspecialchars($roster->remark, ENT_QUOTES) : ''; ?>">
                <label for="remark" class="form-label">Remark</label>
            </div>

            <button type="submit" class="form-btn">Submit</button>
        </form>
    </div>
</main>

<?php
include './views/layouts/footer.php';
?>