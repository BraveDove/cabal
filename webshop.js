import itemData from './CashShop/itemData.js';
const select = document.getElementById('itemSelection');
const selectGrid = document.querySelector('.select-grid');

// Populate select options and grid elements
itemData.forEach(item => {
    // Create select option
    const selectOption = document.createElement('option');
    selectOption.value = item.value;
    selectOption.dataset.itemidx = item.itemidx;
    selectOption.dataset.itemopt = item.itemopt;
    selectOption.dataset.price = item.price; // Include the price dataset attribute
    selectOption.textContent = `${item.label} - Price: ${item.price}`;
    select.appendChild(selectOption);

    // Create grid option
    const gridOption = document.createElement('div');
    gridOption.className = 'select-option';
    gridOption.dataset.value = item.value;
    gridOption.dataset.itemidx = item.itemidx;
    gridOption.dataset.itemopt = item.itemopt;
    gridOption.dataset.price = item.price; // Include the price dataset attribute

    const img = document.createElement('img');
    img.src = item.img;
    img.alt = '';
    gridOption.appendChild(img);

    const span = document.createElement('span');
    span.innerHTML = `${item.label} <br>Price: ${item.price}`;
    span.classList.add('itemTag');
    gridOption.appendChild(span);

    // Create buy button
        const buyButton = document.createElement('button');
        buyButton.textContent = 'Buy';
        buyButton.classList.add('buybtn');
        gridOption.appendChild(buyButton);

    selectGrid.appendChild(gridOption);
});

// JavaScript to synchronize the selected option
const selectOptions = Array.from(selectGrid.children);

selectOptions.forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        select.value = value;

        selectOptions.forEach(opt => {
            opt.classList.remove('selected');
        });

        option.classList.add('selected');
    });
});

// JavaScript to maintain selection focus
select.addEventListener('change', () => {
    const selectedOption = selectOptions.find(option => option.getAttribute('data-value') === select.value);

    selectOptions.forEach(option => {
        option.classList.remove('selected');
    });

    selectedOption.classList.add('selected');
});