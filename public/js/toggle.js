let modal = document.querySelector('#modal');
let body = document.querySelector('body');
let closeButtons = document.querySelectorAll('.close-modal');
let toggle = document.querySelector('#toggle');

toggle.onclick = () => {
    modalHandler(true);
};

closeButtons.forEach((button) => {
    button.addEventListener('click', () => {
        modalHandler(false);
    });
});

const modalHandler = (value) => {
    if (value) {
        body.classList.add('overflow-hidden')
        fadeIn(modal);
    } else {
        body.classList.remove('overflow-hidden')
        fadeOut(modal);
    }
};

const fadeOut = (element) => {
    element.style.opacity = 1;
    (fade = () => {
        if ((element.style.opacity -= 0.1) < 0) {
            element.style.display = 'none';
        } else {
            requestAnimationFrame(fade);
        }
    })();
};

const fadeIn = (element, display) => {
    element.style.opacity = 0;
    element.style.display = display || 'flex';
    (fade = () => {
        let value = parseFloat(element.style.opacity);
        if (!((value += 0.2) > 1)) {
            element.style.opacity = value;
            requestAnimationFrame(fade);
        }
    })();
};