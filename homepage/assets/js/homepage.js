const menuButton = document.querySelector("[data-mobile-menu-button]");
const mobileMenu = document.querySelector("[data-mobile-menu]");

if (menuButton && mobileMenu) {
    menuButton.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
    });
}

const authModal = document.getElementById("authModal");
const authClose = document.querySelector("[data-auth-modal-close]");
const authTabs = document.querySelectorAll("[data-auth-tab]");
const authPanels = document.querySelectorAll("[data-auth-panel]");

document.querySelectorAll("[data-auth-modal-open]").forEach((button) => {
    button.addEventListener("click", () => {
        openAuthModal(button.dataset.authModalOpen || "login");
        mobileMenu?.classList.add("hidden");
    });
});

authClose?.addEventListener("click", closeAuthModal);

authModal?.addEventListener("click", (event) => {
    if (event.target === authModal) {
        closeAuthModal();
    }
});

authTabs.forEach((tab) => {
    tab.addEventListener("click", () => switchAuthTab(tab.dataset.authTab));
});

document.querySelectorAll("[data-password-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
        const target = document.getElementById(button.dataset.target);

        if (!target) {
            return;
        }

        const isPassword = target.type === "password";
        target.type = isPassword ? "text" : "password";
        button.textContent = isPassword ? "Hide" : "Show";
    });
});

function openAuthModal(tab) {
    if (!authModal) {
        return;
    }

    switchAuthTab(tab);
    authModal.classList.remove("hidden");
}

function closeAuthModal() {
    authModal?.classList.add("hidden");
}

function switchAuthTab(activeTab) {
    authTabs.forEach((tab) => {
        const isActive = tab.dataset.authTab === activeTab;
        tab.classList.toggle("bg-white", isActive);
        tab.classList.toggle("text-primary", isActive);
        tab.classList.toggle("shadow-sm", isActive);
        tab.classList.toggle("text-slate-500", !isActive);
    });

    authPanels.forEach((panel) => {
        panel.classList.toggle("hidden", panel.dataset.authPanel !== activeTab);
    });
}
