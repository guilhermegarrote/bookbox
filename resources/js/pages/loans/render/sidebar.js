export function renderSidebar(loans) {
    const list = document.getElementById('loan-list');
    if (!list) return;

    list.innerHTML = '';
    const fragment = document.createDocumentFragment();

    for (const loan of loans) {
        const { color, label, days, quantity } = calculateLoanStatus(loan.days_diff);

        const li = document.createElement('li');
        li.className = 'loan-item';
        li.dataset.loanId = loan.id;

        const colorDiv = document.createElement('div');
        colorDiv.className = 'loan-color';
        colorDiv.style.backgroundColor = color;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'loan-content';

        const leftWrapper = document.createElement('div');

        const titleDiv = document.createElement('div');
        titleDiv.className = 'loan-title';
        titleDiv.textContent = loan.title ?? '';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'loan-label';
        labelDiv.style.color = color;
        labelDiv.textContent = label;

        leftWrapper.appendChild(titleDiv);
        leftWrapper.appendChild(labelDiv);

        const daysWrapper = document.createElement('div');
        daysWrapper.className = 'loan-days';

        const daysNumber = document.createElement('div');
        daysNumber.className = 'loan-days-number';
        daysNumber.textContent = days;

        const daysText = document.createElement('div');
        daysText.className = 'loan-days-text';
        daysText.textContent = quantity;

        daysWrapper.appendChild(daysNumber);
        daysWrapper.appendChild(daysText);

        contentDiv.appendChild(leftWrapper);
        contentDiv.appendChild(daysWrapper);

        li.appendChild(colorDiv);
        li.appendChild(contentDiv);

        fragment.appendChild(li);
    }

    list.appendChild(fragment);
}

function calculateLoanStatus(daysDiff) {
    let color, label, days, quantity;

    days = Math.abs(daysDiff);

    const isLate = daysDiff < 0;
    const isToday = daysDiff === 0;
    const isSoon = daysDiff > 0 && daysDiff <= 7;

    if (isLate) {
        color = '#e74c3c';
        label = 'vencido há:';
        quantity = days === 1 ? 'dia' : 'dias';
    }
    else if (isToday) {
        color = '#e74c3c';
        label = 'vencimento:';
        quantity = 'hoje';
    }
    else if (isSoon) {
        color = '#f39c12';
        label = 'vencimento em:';
        quantity = daysDiff === 1 ? 'dia' : 'dias';
        days = daysDiff;
    }
    else {
        color = '#1fbd60ff';
        label = 'vencimento em:';
        quantity = 'dias';
        days = daysDiff;
    }

    return { color, label, days, quantity };
}
