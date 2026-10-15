<div class="table__row-quantity form-control">
    <div class="form-control__quantity" data-npp="<?= $pkValue ?>" data-content="<?= $tablePk ?>">
        <a href="?action=change_npp&field=<?= $name ?>&<?= $tablePk ?>=<?= $pkValue ?>&up" class="form-control__quantity-minus">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <rect y="6" width="2" height="10" rx="1" transform="rotate(-90 0 6)"
                    fill="white" />
            </svg>
        </a>
        <input type="text" value="<?= $value ?>" class="form-control__quantity-input" />
        <a href="?action=change_npp&field=<?= $name ?>&<?= $tablePk ?>=<?= $pkValue ?>&down" class="form-control__quantity-plus">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <rect x="4" width="2" height="10" rx="1" fill="white" />
                <rect y="6" width="2" height="10" rx="1" transform="rotate(-90 0 6)"
                    fill="white" />
            </svg>
        </a>
    </div>
</div>