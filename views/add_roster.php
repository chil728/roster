<?php
$title = "Add Roster";
include __DIR__ . '/./layouts/header.php';

if (!isset($_SESSION['userID'])) {
    redirect('./login');
    exit();
}

require_once './controllers/Types.php';

$init->loadTypes();
$userTypes = $_SESSION['types'];

?>

<main id="add-roster">

    <div class="form-container">
        <h1>Add Repeat</h1>
        <form action="./controllers/Rosters.php" method="POST">
            <input type="hidden" name="" id="">

            <input type="date" name="" id="">

            <input type="date" name="" id="">

            <select class="form-input" name="weekday[]" id="">
                <option value="Mon">Mon</option>
                <option value="Tue">Tue</option>
                <option value="Wed">Wed</option>
                <option value="Thu">Thu</option>
                <option value="Fri">Fri</option>
                <option value="Sat">Sat</option>
                <option value="Sun">Sun</option>
            </select> 

            <select class="form-input" type="text" name="oldType" id="oldType" placeholder="Old Type">

                <option value="">Choose One</option>

                <?php for ($i = 0; $i < count($userTypes); $i++) : ?>
                    <option value="<?= $userTypes[$i]->type ?>"><?= $userTypes[$i]->type ?></option>
                <?php endfor; ?>

            </select>

            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="form-container">
        <h1>Add Roster</h1>

        <?php flash('add-roster-error'); ?>

        <form action="./controllers/Rosters.php" method="POST">
            <input type="hidden" name="action" value="addRosters">

            <div class="form-group">
                <input type="number" class="form-input" id="cycle" name="cycle" <?php if(isset($_SESSION['cycle']) && $_SESSION['cycle'] != false) echo 'value="' . ($_SESSION['cycle'] + 1) . '"'; ?> placeholder="Cycle">
                <label for="cycle" class="form-label">Cycle</label>
            </div>

            <div class="rosters">
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Remark</th>
                    </tr>
                    <?php for ($i = 0; $i < 14; $i++): ?>
                        <tr>
                            <td><input type="date" class="form-input" name="dates[]"></td>
                            <td>
                                <select class="form-input" name="types[]" id="">
                                    <option value="" default>Choose One</option>
                                    <?php foreach($userTypes as $userType) : ?>
                                        <option value="<?= $userType->type ?>"><?= $userType->type ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" class="form-input" name="remarks[]" id=""></td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>

            <button type="submit" class="form-btn">Submit</button>
        </form>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const cycleInput = document.getElementById("cycle");
        const dateInputs = document.getElementsByName("dates[]");

        // 當 cycle 輸入框改變時，自動填充日期
        cycleInput.addEventListener('change', (e) => {
            const cycleNumber = parseInt(e.target.value);
            if (!isNaN(cycleNumber) && cycleNumber > 0) {
                updateDatesFromCycle(cycleNumber);
            }
        });

        // 當某個日期輸入框改變時，更新其他日期
        dateInputs.forEach((input, index) => {
            input.addEventListener('change', (e) => {
                updateDatesFromDate(index, e.target.value);
            });
        });

        // 根據 cycle 編號計算並填充14天日期
        function updateDatesFromCycle(cycleNumber) {
            const startDate = new Date('2006-12-11');
            const daysToCycleStart = (cycleNumber - 1) * 14;
            startDate.setDate(startDate.getDate() + daysToCycleStart);

            for (let i = 0; i < dateInputs.length; i++) {
                const currentDate = new Date(startDate);
                currentDate.setDate(startDate.getDate() + i);
                dateInputs[i].value = formatDate(currentDate);
            }
        }

        // 當某個日期改變時，更新其他日期
        function updateDatesFromDate(changedIndex, selectedDate) {
            if (!selectedDate) return;

            const baseDate = new Date(selectedDate);

            for (let i = 0; i < dateInputs.length; i++) {
                if (i !== changedIndex) {
                    const offset = i - changedIndex;
                    const newDate = new Date(baseDate);
                    newDate.setDate(baseDate.getDate() + offset);
                    dateInputs[i].value = formatDate(newDate);
                }
            }

            // 更新 cycle 編號
            const firstDate = new Date(dateInputs[0].value);
            const cycleNumber = calculateCycleNumber(firstDate);
            cycleInput.value = cycleNumber;
        }

        // 計算 cycle 編號
        function calculateCycleNumber(date) {
            const startDate = new Date('2006-12-11');
            const diffTime = date - startDate;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            return Math.floor(diffDays / 14) + 1;
        }

        // 格式化日期為 YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // 初始化：如果 cycle 輸入框有值，自動填充日期
        if (cycleInput.value) {
            updateDatesFromCycle(parseInt(cycleInput.value));
        }

        function addWeekDaySelect(event) {
            preventDefault();
        }
    });
</script>

<?php
include './views/layouts/footer.php';
unset($_SESSION['cycle']);
?>