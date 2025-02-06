import FormOrderManager from "./FormOrderManager.js";
import StreamManager from "./StreamManager.js";

export default class TurboManager {
  #PageIsMounting = false;
  #PageIsMounted = false;
  #streamManager;
  #formOrderManager;

  constructor() {
    this.#streamManager = new StreamManager("http://localhost:8082/.well-known/mercure");

    document.addEventListener("turbo:before-render", () => this.#onPageUnmount());
    document.addEventListener("turbo:load", () => this.#onPageMount());
  }

  #onPageMount() {
    if (!this.#streamManager || this.#PageIsMounting || this.#PageIsMounted) return;

    this.#PageIsMounting = true;

    this.#streamManager.initStreams();

    if (FormOrderManager.hasToBeInit()) {
      if (!this.#formOrderManager) {
        this.#formOrderManager = new FormOrderManager();
      }

      this.#formOrderManager.initForm();
    }

    // Order is needed to keep condition working well.
    this.#PageIsMounted = true;
    this.#PageIsMounting = false;
  }

  #onPageUnmount() {
    this.#streamManager.cleanStreams();
    
    if (this.#formOrderManager) this.#formOrderManager.cleanForm();

    this.#PageIsMounted = false;
  }
}
