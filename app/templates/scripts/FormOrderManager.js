export default class FormOrderManager {
  #form;
  #submitButton;
  #radioLabels;
  #radioInputs;
  #activeObserver;
  #eventListeners = new Map();

  static hasToBeInit() {
    return document.querySelector("form[name='order_form']");
  }

  initForm() {
    this.#initListeners();
    this.#initObserver();
  }

  cleanForm() {
    if (this.#eventListeners.size > 0) {
      this.#eventListeners.forEach((listener, element) => {
        element.removeEventListener(listener.eventType, listener.callback);
      });
    }

    if (this.#activeObserver) this.#activeObserver.disconnect();

    this.#eventListeners.clear();
    this.#activeObserver = null;
  }

  #initListeners() {
    this.#form = document.querySelector("form[name='order_form']");
    this.#submitButton = this.#form.querySelector(".form_order__button");
    this.#radioLabels = this.#form.querySelectorAll("#order_form_wave label");
    this.#radioInputs = this.#form.querySelectorAll("#order_form_wave input[type='radio']");

    // Make radio labels focusable.
    this.#radioLabels.forEach((label) => label.setAttribute("tabindex", "0"));

    // Init submit button state.
    this.#updateSubmitButtonState(this.#radioInputs);

    // Make labels interactive with the keyboard for a11y.
    this.#radioLabels.forEach((label) => {
      const listener = (event) =>
        this.#handleLabelAccessInteraction(label, event);

      label.addEventListener("keydown", listener);

      this.#eventListeners.set(label, {
        eventType: "keydown",
        callback: listener,
      });
    });

    // Listen input change for enable form submit button.
    this.#radioInputs.forEach((input) => {
      const listener = () => this.#updateSubmitButtonState(this.#radioInputs);

      input.addEventListener("change", listener);

      this.#eventListeners.set(input, {
        eventType: "change",
        callback: listener,
      });
    });
  }

  #handleLabelAccessInteraction(label, event) {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      const input = this.#form.querySelector(`#${label.getAttribute("for")}`);

      if (input) {
        input.checked = true;
        input.dispatchEvent(new Event("change"));
      }
    }
  }

  #updateSubmitButtonState(radioInputs) {
    const isChecked = Array.from(radioInputs).some((input) => input.checked);
    this.#submitButton.disabled = !isChecked;
    if (isChecked) {
      this.#submitButton.innerText = "Passez commande";
    }
  }

  // Trigger stream form mutation for reinit listeners.
  #initObserver() {
    const observer = new MutationObserver((mutationList) => {
      for (const mutation of mutationList) {
        if (mutation.type === "childList") {
          if (this.#hasFormMutate(mutation)) {
            this.#initListeners();
          }
        }
      }
    });

    observer.observe(this.#form.parentElement || document.body, {
      childList: true,
      subtree: true,
    });

    this.#activeObserver = observer;
  }

  #hasFormMutate(mutation) {
    const inAddedNodes = Array.from(mutation.addedNodes).some(
      (node) => node.id && node.id.includes(this.#form.id)
    );

    const inRemovedNodes = Array.from(mutation.removedNodes).some(
      (node) => node.id && node.id.includes(this.#form.id)
    );

    return inAddedNodes && inRemovedNodes;
  }
}
