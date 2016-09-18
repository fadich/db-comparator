<?php
include_once ('DbComparator.php');

use Comparator\DbComparator; ?>
<html>
<head>
    <title>Database comparator</title>
    <script src="main.js"></script>
    <link   href="main.css" rel="stylesheet">
</head>
<body>
<div class="head">Database comparator</div>
<?php if (empty($_GET)): ?>
    <!-- Hello message -->
    <li>Please, enter params for database connection via GET request.<br>
    <li>Set value of any GET-variable as a string having the structure "[host]%%[username]%%[database]%%[password]".<br>
<?php else:
    foreach ($_GET as $item)
    {
        $connection = explode('%%', $item);
        $databasesCon[] = $connection;
    }
endif;

if (!empty($databasesCon)): ?>
    <!--  Getting info about databases  -->
    <?php try {
        $i = 0;
        foreach ($databasesCon as $databaseCon):
            $bases[] = $db = new DbComparator($databaseCon[0], $databaseCon[1], $databaseCon[2], $databaseCon[3]);
            if (!$db->hasErrors()):
                if ($content = $db->getContent()): ?>
                    <h2 class="structure-title">
                        Structure of "<?= $databaseCon[2] ?>"
                        <span onclick="hide(<?= $i ?>)" id="span-<?= $i ?>"
                              title="Show/hide info about structure of ''<?= $databaseCon[2] ?>''"
                              class="btn-hide">(show)</span>
                    </h2>
                    <div class="structure-body" id="div-<?= $i++ ?>" hidden="hidden">
                        <?php foreach ($content as $key => $table): ?>
                            <ul><font size="4"><strong><?= $key ?></strong></font><br>
                                <?php foreach ($table as $column => $value): ?>
                                    <br><ul><font size="3"><strong><?= $column ?></strong></font>
                                        <?php foreach ($value as $col => $val): ?>
                                        <li><font size="2"><?= $col ?> - <?= $val ?></font>
                                            <?php endforeach; ?>
                                    </ul>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h2 class="structure-title">
                        <font color="#CC5555" size="4">Database "<?= $databaseCon[2] ?>" is empty</font>
                    </h2>
                <?php endif;
            else:
            endif;
        endforeach;
        } catch (\Exception $e) {
            echo '<br><font color="#CC5555" size="4">' . $e->getMessage() . '</font>';
        }
    $length = sizeof($bases);
    if ($length > 1): ?>
    <!--    Getting info about difference    -->
        <hr>
        <?php for ($i = 0; $i < $length; $i++):
            for ($j = $i + 1; $j < $length; $j++):
                if ($i !== $j): ?>
                    <div class="difference-result">
                        <h2 align="center">Comparison "<?= $bases[$i]->getDbName() ?>"
                            and "<?= $bases[$j]->getDbName() ?>"</h2>
                        <?php $compareResult[$i] = $bases[$i]->compare($bases[$j]);
                        $compareResult[$j] = $bases[$j]->compare($bases[$i]); ?>
                        <table>
                            <tr>
                            <?php if (!empty($compareResult[$i])): ?>
                                <td class="table-td">
                                    <font size="4"><strong>Database "<?= $bases[$i]->getDbName() ?>" has:</strong></font><br>
                                    <?php displayResult($compareResult[$i]); ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!empty($compareResult[$j])): ?>
                                <td class="table-td">
                                    <font size="4"><strong>Database "<?= $bases[$j]->getDbName() ?>" has:</strong></font><br>
                                    <?php displayResult($compareResult[$j]); ?>
                                </td>
                            <?php endif; ?>
                                </tr>
                        </table>
                        <?php if (empty($compareResult[$i]) && empty($compareResult[$j])): ?>
                            <h2 align="center"><font size="4" color="green">Databases are identical</font></h2>
                        <?php endif; ?>
                    </div>
                <?php endif;
            endfor;
        endfor;
    endif;
endif; ?>
</body>
</html>
<?php function displayResult($compare)
{
    foreach ($compare as $key => $table):
        if (!is_array($table)): ?>
            <br>table <strong><font color="#CC5555"><?= $key ?></font></strong>
        <?php else:
            foreach ($table as $ke => $column):
                if (!is_array($column)): ?>
                    <br>column <strong><font color="#CC5555"><?= $ke ?></font></strong> in table
                    <strong><?= $key ?></strong>
                <?php else:
                    foreach ($column as $k => $type): ?>
                        <br>type of column <strong><?= $ke ?></strong> (in table <strong><?= $key ?></strong>) is
                        <font color="#CC5555"><?= $k ?> - <?= $type ?></font>
                    <?php endforeach;
                endif;
            endforeach;
        endif;
    endforeach;
} ?>

