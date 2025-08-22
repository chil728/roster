<?php

$title = 'Home';

include './views/layouts/header.php';

function calculateCycle($startDate, $targetDate)
{
    $start = new DateTime($startDate);
    $target = new DateTime($targetDate);
    $interval = $start->diff($target);
    $days = $interval->days;
    $cycleNumber = floor($days / 14) + 1; // 當前週期編號
    $daysToCycleStart = ($cycleNumber - 1) * 14; // 當前週期的起始天數
    $cycleStart = clone $start;
    $cycleStart->modify("+$daysToCycleStart days");
    $cycleEnd = clone $cycleStart;
    $cycleEnd->modify("+55 days"); // 4個週期共56天（14 * 4 - 1）
    return [
        'cycle_number' => $cycleNumber,
        'cycle_start' => $cycleStart->format('Y-m-d'),
        'cycle_end' => $cycleEnd->format('Y-m-d'),
        'cycle_range' => range($cycleNumber, $cycleNumber + 3) // 返回4個週期編號
    ];
}

function getCycleDates($cycleStartDate)
{
    $dates = [];
    $current = new DateTime($cycleStartDate);
    for ($i = 0; $i < 56; $i++) { // 4個週期共56天
        $dates[] = $current->format('Y-m-d');
        $current->modify('+1 day');
    }
    return $dates;
}

if (isset($_SESSION['userID'])) {
    require_once './controllers/Rosters.php';
    require_once './controllers/Types.php';
    $rostersInit = new Rosters;
    $typesInit = new Types;

    if (!isset($_SESSION['cycle']) || !isset($_SESSION['rosters'])) {
        $rostersInit->index();
    }

    $typesInit->loadTypes();
    $userTypes = $_SESSION['types'];
    unset($_SESSION['types']);

    $today = date('Y-m-d');
    $todayCycleData = calculateCycle('2006-12-11', $today);
    $todayCycle = $todayCycleData['cycle_number'];
    $_SESSION['cycle'] = (int) ($_SESSION['cycle'] ?? $todayCycle);
    $currCycle = $_SESSION['cycle'];
    $cycleStart = (new DateTime('2006-12-11'))->modify("+" . (($currCycle - 1) * 14) . " days")->format('Y-m-d');
    $cycleDates = getCycleDates($cycleStart);
    $rosters = $_SESSION['rosters'] ?? [];

    // 構建4個週期（8週）的roster數據
    $rosterData = [];
    foreach ($cycleDates as $index => $date) {
        $roster = null;
        foreach ($rosters as $r) {
            if ($r->date == $date) {
                $roster = $r;
                break;
            }
        }
        
        $rosterData[] = [
            'date' => $date,
            'roster' => $roster,
            'formatted_date' => (new DateTime($date))->format('j M Y'),
            'is_today' => $date == $today,
            'week' => floor($index / 7)
        ];
    }
}
?>

<main id="home">
    <section id="hero">
        <?php
        // echo var_dump($_SESSION['cycle']);
        // echo '<br>';
        // echo var_dump($_GET);
        // echo var_dump($userTypes[0]);
        ?>
    </section>

    <section id="view-rosters">
        <?php flash('login-success'); ?>
        <?php flash('add-roster-success'); ?>
        <?php flash('edit-roster-success'); ?>
        <?php flash('delete-roster-success'); ?>
        <?php flash('delete-roster-error'); ?>
        <?php flash('no-record'); ?>

        <?php if (isset($_SESSION['userID'])) : ?>
            <div class="week-title">
                <span class="week">Mon</span>
                <span class="week">Tue</span>
                <span class="week">Wed</span>
                <span class="week">Thu</span>
                <span class="week">Fri</span>
                <span class="week">Sat</span>
                <span class="week">Sun</span>
            </div>

            <div class="pages">
                <form action="./controllers/Rosters.php" method="POST">
                    <input type="hidden" name="action" value="getPrevRosters">
                    <input type="hidden" name="cycle" value="<?php echo $currCycle - 4; ?>">
                    <button type="submit" <?php echo $currCycle <= 1 ? 'disabled="disabled"' : ''; ?>><i class="ri-arrow-left-line"></i></button>
                </form>
                <form action="./controllers/Rosters.php" method="POST">
                    <input type="hidden" name="action" value="getTodayRosters">
                    <input type="hidden" name="cycle" value="<?php echo calculateCycle('2006-12-11', date('Y-m-d'))['cycle_number']; ?>">
                    <button type="submit">Today</button>
                </form>
                <form action="./controllers/Rosters.php" method="POST">
                    <input type="hidden" name="action" value="getNextRosters">
                    <input type="hidden" name="cycle" value="<?php echo $currCycle + 4; ?>">
                    <button type="submit"><i class="ri-arrow-right-line"></i></button>
                </form>
            </div>

            <?php for ($week = 0; $week < 8; $week++) : ?>
                <div class="rosters-container">
                    <?php for ($i = $week * 7; $i < ($week + 1) * 7; $i++) : ?>
                        <div class="roster-card">

                            <div class="roster-date <?php echo $rosterData[$i]['is_today'] ? 'today' : ''; ?>"
                                <?php
                                    if ($rosterData[$i]['date'] < $today) {
                                        echo 'style="' . 'background-color: ' . 'rgba(0, 0, 0, 0.5)' . '; ' . '"';
                                    }
                                ?>
                            >
                                <?php echo $rosterData[$i]['formatted_date']; ?>
                            </div>

                            <div class="roster-type"
                                <?php 
                                    foreach ($userTypes as $userType) {
                                        if ($userType->type == isset($rosterData[$i]['roster']) ? $rosterData[$i]['roster']->type : '') {
                                            echo 'style="' . 'background-color: ' . $userType->bgColor . '; ' . '"';
                                        }
                                    }
                                ?>
                            >

                                <a href="./edit_roster?cycle=<?php echo $currCycle + floor($i / 14); ?>&<?php echo $rosterData[$i]['roster'] ? 'roster_id=' . $rosterData[$i]['roster']->id : 'date=' . htmlspecialchars($rosterData[$i]['date'], ENT_QUOTES); ?>"
                                    <?php 
                                        foreach ($userTypes as $userType) {
                                            if ($userType->type == isset($rosterData[$i]['roster']) ? $rosterData[$i]['roster']->type : '') {
                                                echo 'style="' . 'color: ' . $userType->fontColor . '; ' . '"';
                                            }
                                        }
                                    ?>
                                >
                                    <?php echo $rosterData[$i]['roster'] ? htmlspecialchars($rosterData[$i]['roster']->type) : ''; ?>
                                </a>

                            </div>

                            <div class="roster-remark" <?php if ($rosterData[$i]['roster'] && strlen($rosterData[$i]['roster']->remark) > 0) echo 'data-remark="' . htmlspecialchars($rosterData[$i]['roster']->remark, ENT_QUOTES) . '"'; ?>>
                                <?php echo $rosterData[$i]['roster'] && strlen($rosterData[$i]['roster']->remark) > 0 ? '<i class="ri-gemini-fill"></i>' : ''; ?>
                            </div>

                        </div>
                    <?php endfor; ?>
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </section>

    <!-- Remark Popup Modal -->
    <div id="remark-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close"><i class="ri-close-line"></i></span>
            <h2>Remark</h2>
            <p id="remark-text"></p>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('remark-modal');
        const remarkText = document.getElementById('remark-text');
        const closeBtn = modal.querySelector('.close');

        // Handle remark click
        document.querySelectorAll('.roster-remark').forEach(remark => {
            remark.addEventListener('click', () => {
                const remarkContent = remark.getAttribute('data-remark');
                if (remarkContent) {
                    remarkText.textContent = remarkContent;
                    modal.style.display = 'flex';
                }
            });
        });

        // Close modal
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

<?php

unset($_SESSION['cycle']);
unset($_SESSION['rosters']);


include './views/layouts/footer.php';
?>