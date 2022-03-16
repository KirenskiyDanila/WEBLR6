    <?php
    $Ticket = new Ticket();
    $rows = $Ticket->getRows($_GET['id']);
    ?>
    <?php foreach ($rows as $row): ?>
    <?php ob_start() ?>
    <div class="item" id="<?= $row['id'] ?>">
        <img src="<?= $row['photo'] ?>" class="item_photo">
        <div class="item_text">
            <a href="ticket.php?id=<?= $row['id'] ?>" style="color: #f58142"><div><?= $row['name'] ?></div></a>
            <div><?= $row['price'] ?> рублей</div>
        </div>
    </div>
    <?php $tickets[] = ob_get_clean() ?>
    <?php endforeach;
    echo json_encode($tickets);
