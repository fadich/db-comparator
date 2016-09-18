<?php
include_once ('DbComparator.php');

use Comparator\DbComparator; ?>
<h1 align="center">Database comparator</h1>
<?php if (empty($_GET)): ?>
    <!-- Hello message --><hr>
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
    <hr>
    <?php try {
            foreach ($databasesCon as $databaseCon):
            $bases[] = $db = new DbComparator($databaseCon[0], $databaseCon[1], $databaseCon[2], $databaseCon[3]);
            if (!$db->hasErrors()):
                if ($content = $db->getContent()): ?>
                    <h2>Tables of <?= $databaseCon[1] ?>:</h2>
                    <?php foreach ($content as $key => $table): ?>
                        <ul><font size="4"><strong><?= $key ?></strong></font><br>
                        <?php foreach ($table as $column => $value): ?>
                            <ul><font size="3"><strong><?= $column ?></strong></font>
                            <?php foreach ($value as $col =>  $val): ?>
                                <li><font size="2"><?= $col ?> - <?= $val ?></font>
                            <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php else: ?>
                    Database <?= $databaseCon[1] ?>is empty
                <?php endif;
            else:
            endif;
        endforeach;
        } catch (\Exception $e) {
            echo '<br>' . $e->getMessage();
        }
    $length = sizeof($bases);
    if ($length > 1): ?>
        <hr>
        <?php for ($i = 0; $i < $length; $i++):
            for ($j = $i + 1; $j < $length; $j++):
                if ($i !== $j): ?>
                    <h2 align="center">Comparison <?= $bases[$i]->getDbName() ?>
                        and <?= $bases[$j]->getDbName() ?></h2>
                    <?php $compareResult[$i] = $bases[$i]->compare($bases[$j]);
                    $compareResult[$j] = $bases[$j]->compare($bases[$i]); ?>
                    <table>
                        <tr>
                        <?php if (!empty($compareResult[$i])): ?>
                            <td style="width: 50vw" align="center">
                                <font size="4"><strong>Database <?= $bases[$i]->getDbName() ?> has:</strong></font><br>
                                <?php displayResult($compareResult[$i]); ?>
                            </td>
                        <?php endif; ?>
                        <?php if (!empty($compareResult[$j])): ?>
                            <td style="width: 50vw" align="center">
                                <font size="4"><strong>Database <?= $bases[$j]->getDbName() ?> has:</strong></font><br>
                                <?php displayResult($compareResult[$j]); ?>
                            </td>
                        <?php endif; ?>
                            </tr>
                    </table>
                    <?php if (empty($compareResult[$i]) && empty($compareResult[$j])): ?>
                        <h2><font size="4" color="green">Databases are identical</font></h2>
                    <?php endif;
                endif;
            endfor;
        endfor;
    endif;
endif;

function displayResult($compare)
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
}
