/**
 * MythicalFramework.js
 *
 * This file is the entry point for the MythicalFramework frontend.
 */

// This value will enable the inspect element thing!
// If you want to disable it, set it to false.
const isDebugMode = true;
// This value will show a warning when debug mode is enabled.
const showDebugWarning = false;



if (isDebugMode == true && showDebugWarning == true) {
    console.warn(
        'Debug mode is enabled. This should only be enabled for development purposes. Do not enable this in production environments.'
    );
    Swal.fire({
        title: 'Debug Mode Enabled',
        text: 'Debug mode is enabled. This should only be enabled for development purposes. Do not enable this in production environments.',
        icon: 'warning',
        confirmButtonText: 'OK',
    });
}

/**
 * Warn users about the danger of pasting something in the browser console.
 */
if (window.console && window.console.log) {
    if (isDebugMode == false) {
        setInterval(function () {
            console.log('%cSTOP!', 'color: red; font-size: 50px; font-weight: bold; text-shadow: 1px 1px black;');
            console.log('%cPlease do not paste any code into the console.', 'color: red; font-size: 20px;');
            console.log(
                '%cPasting code into the console can compromise your account and result in suspension or hacking.',
                'color: red; font-size: 14px;'
            );
        }, Math.floor(Math.random() * 4000) + 1000);
    }
}

/**
 * MythicalFramework Dialogs
 */

document.addEventListener('keydown', function (event) {
    if (event.ctrlKey && event.keyCode === 68) {
        event.preventDefault();
        Swal.fire({
            title: 'Enter Dialog Number',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
            },
            icon: 'question',
            text: 'Enter the number of the dialog you would like to open.',
            showCancelButton: true,
            confirmButtonText: 'Go',
            showLoaderOnConfirm: true,
            preConfirm: (userInput) => {
                if (userInput === null || userInput.trim() === '') {
                    window.location.href = '/dashboard';
                } else {
                    switch (userInput) {
                        case '1':
                            window.location.href = '/dashboard';
                            break;
                        default:
                            Swal.fire({
                                title: 'Invalid Dialog',
                                text: 'The dialog number you entered is invalid. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                    }
                }
            },
        });
    }
});

/**
 * MythicalGuard Blocker
 *
 * This script checks if the current website is blocked by MythicalGuard.
 *
 * If the website is blocked, the user will be alerted and the page will be cleared.
 */
const websiteUrl = window.location.hostname;
const blockedWebsitesYamlUrl = 'https://raw.githubusercontent.com/MythicalLTD/MythicalGuard/main/blocked.yml';

fetch(blockedWebsitesYamlUrl)
    .then((response) => response.text())
    .then((yamlText) => jsyaml.load(yamlText))
    .then((blockedWebsites) => {
        const blockedWebsite = blockedWebsites.find((site) => site.url === websiteUrl);
        if (blockedWebsite) {
            console.error(`${websiteUrl} is blocked for using the service. Reason: ${blockedWebsite.reason}`);
            Swal.fire({
                title: 'Website Blocked',
                text: `This website (${websiteUrl}) is blocked for using the service. Reason: ${blockedWebsite.reason}`,
                icon: 'error',
                confirmButtonText: 'OK',
            });
            document.write('');
        } else {
            console.log(`${websiteUrl} is not blocked for using the service.`);
        }
    })
    .catch((error) => console.error(error));

/**
 * Prevent users from using the browser console.
 */
if (isDebugMode == false) {
    // take body to change the content
    const body = document.getElementsByTagName('body');
    // stop keyboard shortcuts
    window.addEventListener('keydown', (event) => {
        if (event.ctrlKey && (event.key === 'S' || event.key === 's')) {
            event.preventDefault();
            document.getElementById('search-box').focus();
        }

        if (event.ctrlKey && event.key === 'C') {
            event.preventDefault();
            showInspectBlockedAlert();
        }
        if (event.ctrlKey && (event.key === 'E' || event.key === 'e')) {
            event.preventDefault();
            showInspectBlockedAlert();
        }
        if (event.ctrlKey && (event.key === 'I' || event.key === 'i')) {
            event.preventDefault();
            showInspectBlockedAlert();
        }
        if (event.ctrlKey && (event.key === 'K' || event.key === 'k')) {
            event.preventDefault();
            showInspectBlockedAlert();
        }
        if (event.ctrlKey && (event.key === 'U' || event.key === 'u')) {
            event.preventDefault();
            showInspectBlockedAlert();
        }
    });

    function showInspectBlockedAlert() {
        Swal.fire({
            title: 'This action is blocked',
            text: 'We have blocked this to protect your account and to prevent malicious use.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
    }
}

/**
 * Search functionality
 */
window.addEventListener('keydown', (event) => {
    if (event.ctrlKey && (event.key === 'S' || event.key === 's')) {
        event.preventDefault();
        document.getElementById('search-box').focus();
    }
});

/**
 * MythicalFramework Utilities
 *
 * This script has some utilities that can be used in the frontend.
 */

/**
 * Alert helpers
 */

/**
 * Show a success alert
 *
 * @param {string} title
 * @param {string} text
 */
function showSuccessAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        confirmButtonText: 'OK',
    });
}
/**
 * Show an error alert
 *
 * @param {string} title
 * @param {string} text
 */
function showErrorAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        confirmButtonText: 'OK',
    });
}
/**
 * Show a warning
 *
 * @param {string} title
 * @param {string} text
 */
function showWarningAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        confirmButtonText: 'OK',
    });
}

/**
 * Show a confirmation dialog
 * 
 * @param {string} title 
 * @param {string} text 
 * @param {string} confirmButtonText 
 * @param {string} cancelButtonText 
 * 
 * @return {boolean}
 */
function showDialogAction(title, text, confirmButtonText, cancelButtonText) {
    Swal.fire({
        title: title,
        text: text,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            return true;
        } else {
            return false;
        }
    });
}

/**
 * 
 * Show a confirm dialog and redirect the user to a specific URL.
 * 
 * @param {string} title 
 * @param {string} text 
 * @param {string} icon_name
 * @param {string} confirmButtonText 
 * @param {string} cancelButtonText 
 * @param {string} redirectUrl 
 */
function showConfirmDialogAndRedirect(title, text, icon_name, confirmButtonText, cancelButtonText, redirectUrl) {
    const validIcons = ['success', 'error', 'warning', 'info', 'question'];
    const icon = validIcons.includes(icon_name) ? icon_name : 'info';
    
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show a confirm action with a text input!
 * 
 * @param {string} title 
 * @param {string} text 
 * @param {string} confirmButtonText 
 * @param {string} cancelButtonText 
 * 
 * @return {string}
 */
function showDialogWithInput(title, text, confirmButtonText, cancelButtonText) {
    Swal.fire({
        title: title,
        text: text,
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
        },
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value === null || result.value.trim() === '') {
                return "";
            } else {
                return result.value;
            }
        } else {
            return "";
        }
    });
}

/**
 * Highlight active sidebar menu item based on current URL
 */
document.addEventListener('DOMContentLoaded', function () {
    const currentUrl = window.location.pathname;
    const menuItems = document.querySelectorAll('#layout-menu .menu-item a');

    menuItems.forEach((menuItem) => {
        const menuItemUrl = menuItem.getAttribute('href');
        if (menuItemUrl === currentUrl) {
            menuItem.classList.add('active');
            const parentMenuItem = menuItem.closest('.menu-item');
            if (parentMenuItem) {
                parentMenuItem.classList.add('active');
                const parentMenuToggle = parentMenuItem.querySelector('.menu-toggle');
                if (parentMenuToggle) {
                    parentMenuToggle.classList.add('active');
                }
            }
        }
    });
});