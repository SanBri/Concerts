let userIcon = document.getElementById('userIcon')
let activeUserIcon = document.getElementById('activeUserIcon')
let menu = document.querySelector('.user-parameters--active')
let body = document.querySelector('.container__body')
let header = document.querySelector('.header__home')
let footer = document.querySelector('.container__footer')

class UserMenu {

    showMenu() {
        menu.style.display = "flex";
        userIcon.style.display = "none";
        activeUserIcon.style.display = "block";
        activeUserIcon.style.color = "purple";
    }

    hideMenu() {
        menu.style.display = "none";
        userIcon.style.display = "block";
        activeUserIcon.style.display = "none";
    }

}

let userMenu = new UserMenu;

if (userIcon) {
    
    if (menu.style.display != 'flex') {
        userIcon.addEventListener('click', () => {
            userMenu.showMenu();
        })
    } 

    if (menu.style.display != 'none') {
        header.addEventListener('click', () => {
            userMenu.hideMenu();
        })
        activeUserIcon.addEventListener('click', () => {
            userMenu.hideMenu();
        })
        body.addEventListener('click', () => {
            userMenu.hideMenu();
        })
        footer.addEventListener('click', () => {
            userMenu.hideMenu();
        })
    }
}
