<?php

$title = "Type";

include __DIR__ . '/./layouts/header.php';

require_once __DIR__ . '/../controllers/Types.php';

$init->loadTypes();
$userTypes = $_SESSION['types'];

?>

<main id="type">
    <section id="add-type">
        <div class="form-container">
            <h1>Add Type</h1>
            <?php flash('add-type-error') ?>
            <form action="./controllers/Types.php" method="POST">
                <input type="hidden" name="action" value="add-type">
                <div class="form-group">
                    <input class="form-input" type="text" name="type" id="type" placeholder="Type">
                    <label class="form-label" for="type">Type</label>
                </div>
                <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                    <div class="form-group">
                        <input class="form-input" type="color" name="bgColor" id="bgColor" placeholder="Background Color">
                        <label class="form-label" for="bgColor">Background Color</label>
                    </div>
    
                    <div class="form-group">
                        <input class="form-input" type="color" name="fontColor" id="fontColor" placeholder="Font Color">
                        <label class="form-label" for="fontColor">Font Color</label>
                    </div>
                </div>
                <button class="form-btn" type="submit">Submit</button>
            </form>
        </div>
    </section>

    <section id="edit-type">
        <div class="form-container">
            <h1>Edit Type</h1>
            <?php flash('edit-type-error') ?>
            <form action="./controllers/Types.php" method="POST">
                <input type="hidden" name="action" value="edit-type">

                <div class="form-group">
                    <select class="form-input" type="text" name="oldType" id="oldType" placeholder="Old Type">

                        <option value="">Choose One</option>

                        <?php for ($i = 0; $i < count($userTypes); $i++) : ?>
                            <option value="<?= $userTypes[$i]->type ?>"><?= $userTypes[$i]->type ?></option>
                        <?php endfor; ?>

                    </select>
                    <label class="form-label" for="oldType">Old Type</label>
                </div>

                <div class="form-group">
                    <input class="form-input" type="text" name="newType" id="newType" placeholder="New Type">
                    <label class="form-label" for="newType">New Type</label>
                </div>
                
                <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                    <div class="form-group">
                        <input class="form-input" type="color" name="bgColor" id="bgColor" placeholder="Background Color">
                        <label class="form-label" for="bgColor">Background Color</label>
                    </div>
    
                    <div class="form-group">
                        <input class="form-input" type="color" name="fontColor" id="fontColor" placeholder="Font Color">
                        <label class="form-label" for="fontColor">Font Color</label>
                    </div>
                </div>

                <button class="form-btn" type="submit">Submit</button>
            </form>
        </div>
    </section>
</main>

<script>
    const userTypes = <?php echo json_encode($userTypes); ?>;

    const editForm = document.querySelector("#edit-type .form-container form");
    const typeSelect = editForm.querySelector(".form-group #oldType");
    const typeInput = editForm.querySelector(".form-group #newType");
    const bgColor = editForm.querySelector(".form-group #bgColor");
    const fontColor = editForm.querySelector(".form-group #fontColor");

    typeSelect.addEventListener("change", () => {

        const selectedType = typeSelect.value;
        const selectedUserType = userTypes.find(userType => userType.type === selectedType);

        typeInput.value = typeSelect.value;

        if (selectedUserType) {
            bgColor.value = selectedUserType.bgColor;
            fontColor.value = selectedUserType.fontColor;
        } else {
            bgColor.value = '#000000';
            fontColor.value = '#000000';
        }
    });

    typeSelect.dispatchEvent(new Event('change'));
</script>

<?php

include __DIR__ . '/./layouts/footer.php';

?>