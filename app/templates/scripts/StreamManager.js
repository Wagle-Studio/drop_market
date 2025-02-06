export default class StreamManager {
  #baseUrl;
  #activeEventSources = new Map();
  #activeObservers = new Set();
  #formListeners = new Map();
  #listening = false;

  constructor(baseUrl) {
    if (!baseUrl)
      throw new Error("Base URL is required to initialize StreamManager.");

    this.#baseUrl = baseUrl;
  }

  initStreams() {
    if (this.#listening) return;

    this.#listening = true;
    this.#initListeners();
    this.#hookFormSubmission();
  }

  cleanStreams() {
    // Disconnect every DOM mutation observers.
    this.#activeObservers.forEach((observer) => {
      observer.disconnect();
    });

    // Remove every form hooks.
    this.#formListeners.forEach((listener, form) => {
      form.removeEventListener("submit", listener);
    });

    // Close every active streams.
    this.#activeEventSources.forEach((eventSource, topicUrl) => {
      try {
        eventSource.close();
      } catch (error) {
        console.error(`Error closing event source: ${error}`);
      }
    });

    this.#activeEventSources.clear();
    this.#activeObservers.clear();
    this.#formListeners.clear();
    this.#listening = false;
  }

  // Iterates through DOM stream components and launches the event source listener.
  #initListeners() {
    document.querySelectorAll("[data-topic]").forEach((streamComponent) => {
      const topic = streamComponent.dataset.topic;

      if (!topic) return;

      const topicUrl = `${this.#baseUrl}?topic=${topic}`;

      if (this.#activeEventSources.has(topicUrl)) return;

      const eventSource = new EventSource(topicUrl, { withCredentials: true });

      // Stack event sources for use as reference to clean.
      this.#activeEventSources.set(topicUrl, eventSource);

      eventSource.onmessage = (event) => {
        this.#handleEventSourceMessage(event);
      };
    });
  }

  // Handles stream event traitment and stream execution.
  #handleEventSourceMessage(event) {
    try {
      const freshStreamComponents = JSON.parse(event.data);

      if (!Array.isArray(freshStreamComponents)) {
        console.warn("Stream data is not an array:", event.data);
        return;
      }

      // Execute Turbo rendering.
      freshStreamComponents.forEach((streamComponent) =>
        Turbo.renderStreamMessage(streamComponent)
      );

      // Observe Turbo DOM mutations to reinitialize stream listeners.
      const domObserver = new MutationObserver((mutationsList, observer) => {
        observer.disconnect();
        this.#initListeners();
      });

      domObserver.observe(document.body, {
        childList: true,
        subtree: true,
      });

      // Stack active observers for use as reference to clean.
      this.#activeObservers.add(domObserver);
    } catch (error) {
      throw new Error(`Error handling event source message : ${error}`);
    }
  }

  // Iterates through DOM forms to listen submissions and hook them.
  #hookFormSubmission() {
    const forms = document.getElementsByTagName("form");

    Array.from(forms).forEach((form) => {
      const listener = () => {
        // Clean connections to avoid stream events to happen.
        this.cleanStreams();
      };

      form.addEventListener("submit", listener);

      // Stack form listeners for use as reference to clean.
      this.#formListeners.set(form, listener);
    });
  }
}
