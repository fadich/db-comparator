<?php

use \royal\type\Mixed;
use comparator\core\DbComparator;

/** @var DbComparator[] $bases */

?>
<html>
<head>
    <title>Database comparator</title>
    <script src="/js/main.js"></script>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <link href="/style/main.css" rel="stylesheet">
</head>
<body>
<div class="head">Database comparator</div>
<?php if (empty($_GET)): ?>
<!-- Hello message --><hr>
<li>Please, enter params for database connection via GET request.<br>
<li>Set value of any GET-variable as a string having the structure "[host]%%[username]%%[database]%%[password]".<br>
    <?php else:
        try {
            foreach ($_GET as $item) {
                $databasesCon[] = explode('%%', $item);
            }
        } catch (\Exception $e) {
            die("" . $e->getMessage());
        }
    endif;

    if (!empty($databasesCon)): ?>
        <!--  Getting info about databases structures -->
        <?php try {
            $i = 0;
            foreach ($databasesCon as $databaseCon):
                $bases[] = $db = new DbComparator($databaseCon[0] ?? null, $databaseCon[1] ?? null, $databaseCon[2] ?? null, $databaseCon[3] ?? null);
                if (!$db->hasErrors()):
                    if ($content = $db->getContent()): ?>
                        <h2 class="structure-title">
                            Structure of "<?= $databaseCon[0] . "@" .$databaseCon[2] ?>"
                            <span onclick="hide(<?= $i ?>)" id="span-<?= $i ?>"
                                  title="Show/hide info about structure of <?= $databaseCon[0] . "@" .$databaseCon[2] ?>"
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
                            <font color="#CC5555" size="4">Database "<?= $databaseCon[0] . "@" .$databaseCon[2] ?>" is empty</font>
                        </h2>
                    <?php endif;
                else: ?>
                    <font color="#CC5555"><?= implode('<br>', $db->errors); ?></font>
                <?php endif;
            endforeach;
        } catch (\Exception $e) {
            echo '<br><font color="#CC5555" size="4">' . $e->getMessage() . '</font>';
        }
        $length = sizeof($bases ?? []);
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
                                    <td class="table-td">
                                        <?php if (!empty($compareResult[$i])): ?>
                                            <button id="join_<?= $i ?>_<?= $j ?>" onclick="join(
                                                        '<?= implodeProperties($bases[$i]) ?>',
                                                        '<?= implodeProperties($bases[$j]) ?>',
                                                        'join_<?= $i ?>_<?= $j ?>'
                                                    )"> >> </button><br>
                                            <font size="4"><strong>Database "<?= $bases[$i]->getDbName() ?>" has:</strong></font><br>
                                            <?php displayResult($compareResult[$i]); ?>
<!--                                        --><?php //else: ?>
<!--                                            --><?php //if ($bases[$j]->isEmpty): ?>
<!--                                                <h3>(empty)</h3>-->
<!--                                            --><?php //endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-td">
                                        <?php if (!empty($compareResult[$j])): ?>
                                            <button id="join_<?= $j ?>_<?= $i ?>" onclick="join(
                                                '<?= implodeProperties($bases[$j]) ?>',
                                                '<?= implodeProperties($bases[$i]) ?>',
                                                'join_<?= $j ?>_<?= $i ?>'
                                             )"> << </button><br>
                                            <font size="4"><strong>Database "<?= $bases[$j]->getDbName() ?>" has:</strong></font><br>
                                            <?php displayResult($compareResult[$j]); ?>
<!--                                         --><?php //else: ?>
<!--                                            --><?php //if ($bases[$j]->isEmpty): ?>
<!--                                                <h3>(empty)</h3>-->
<!--                                            --><?php //endif; ?>
                                        <?php endif; ?>
                                    </td>
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

<?php function implodeProperties(DbComparator $base) {
    $properties = [
        'host'     => $base->host,
        'username' => $base->username,
        'database' => $base->database,
        'password' => $base->password,
    ];
    return (new Mixed($properties))->implodeElements('%%', '=>')->value;
}