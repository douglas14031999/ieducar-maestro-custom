<script>
import FroalaEditor from 'froala-editor';
import { h } from 'vue';
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['tag', 'modelValue', 'config', 'onManualControllerReady'],
  emits: ['update:modelValue', 'input'],
  data: () => ({
    initEvents: [],
    // Tag on which the editor is initialized.
    currentTag: 'div',
    // Editor element.
    f_editor: null,
    // Current config.
    currentConfig: null,
    // Editor options config
    defaultConfig: {
      immediateVueModelUpdate: false,
      vueIgnoreAttrs: null,
    },
    editorInitialized: false,
    SPECIAL_TAGS: ['img', 'button', 'input', 'a'],
    INNER_HTML_ATTR: 'innerHTML',
    hasSpecialTag: false,
    oldModel: null,
  }),
  computed: {
    model: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      },
    },
  },
  watch: {
    model() {
      this.updateValue();
    },
  },
  created() {
    this.currentTag = this.tag || this.currentTag;
  },
  // After first time render.
  mounted() {
    if (this.SPECIAL_TAGS.indexOf(this.currentTag) !== -1) {
      this.hasSpecialTag = true;
    }
    if (this.onManualControllerReady) {
      this.generateManualController();
    } else {
      this.createEditor();
    }
  },
  beforeUnmount() {
    this.destroyEditor();
  },
  methods: {
    updateValue() {
      if (JSON.stringify(this.oldModel) === JSON.stringify(this.model)) {
        return;
      }
      this.setContent();
    },
    createEditor() {
      if (this.editorInitialized) {
        return;
      }
      this.currentConfig = this.clone(this.config || this.defaultConfig);
      this.currentConfig = { ...this.currentConfig };
      this.setContent(true);
      // Bind editor events.
      this.registerEvents();
      this.initListeners();
      this.f_editor = new FroalaEditor(this.$el, this.currentConfig);
      this.editorInitialized = true;
    },
    // Return clone object
    clone(item) {
      // eslint-disable-next-line @typescript-eslint/no-this-alias
      const me = this;
      if (!item) {
        return item;
      } // null, undefined values check
      const types = [Number, String, Boolean];
      let result;
      // normalizing primitives if someone did new String('aaa'), or new Number('444');
      types.forEach((type) => {
        if (item instanceof type) {
          result = type(item);
        }
      });
      if (typeof result === 'undefined') {
        if (Object.prototype.toString.call(item) === '[object Array]') {
          result = [];
          item.forEach((child, index) => {
            result[index] = me.clone(child);
          });
        } else if (typeof item === 'object') {
          // testing that this is DOM
          if (item.nodeType && typeof item.cloneNode === 'function') {
            result = item.cloneNode(true);
          } else if (!item.prototype) {
            // check that this is a literal
            if (item instanceof Date) {
              result = new Date(item);
            } else {
              // it is an object literal
              result = {};
              // eslint-disable-next-line
              for (const key in item) {
                result[key] = me.clone(item[key]);
              }
            }
          } else {
            result = item;
          }
        } else {
          result = item;
        }
      }
      return result;
    },
    setContent(firstTime) {
      if (!this.editorInitialized && !firstTime) {
        return;
      }
      if (this.model || this.model === '') {
        this.oldModel = this.model;
        if (this.hasSpecialTag) {
          this.setSpecialTagContent();
        } else {
          this.setNormalTagContent(firstTime);
        }
      }
    },
    setNormalTagContent(firstTime) {
      // eslint-disable-next-line @typescript-eslint/no-this-alias
      const self = this;
      function htmlSet() {
        if (self.f_editor.html) {
          self.f_editor.html.set(self.model || '');
        }
        // This will reset the undo stack everytime the model changes externally. Can we fix this?
        self.f_editor.undo.saveStep();
        self.f_editor.undo.reset();
      }
      if (firstTime) {
        this.registerEvent('initialized', () => {
          htmlSet();
        });
      } else {
        htmlSet();
      }
    },
    setSpecialTagContent() {
      const tags = this.model;
      // add tags on element
      if (tags) {
        // eslint-disable-next-line
        for (var attr in tags) {
          // eslint-disable-next-line
          if (tags.hasOwnProperty(attr) && attr !== this.INNER_HTML_ATTR) {
            this.$el.setAttribute(attr, tags[attr]);
          }
        }
        // eslint-disable-next-line
        if (tags.hasOwnProperty(this.INNER_HTML_ATTR)) {
          this.$el.innerHTML = tags[this.INNER_HTML_ATTR];
        }
      }
    },
    destroyEditor() {
      if (this.f_editor) {
        this.f_editor.destroy();
        this.editorInitialized = false;
        this.f_editor = null;
      }
    },
    getEditor() {
      return this.f_editor;
    },
    generateManualController() {
      const controls = {
        initialize: this.createEditor,
        destroy: this.destroyEditor,
        getEditor: this.getEditor,
      };
      this.onManualControllerReady(controls);
    },
    updateModel() {
      let modelContent = '';
      if (this.hasSpecialTag) {
        const attributeNodes = this.$el[0].attributes;
        const attrs = {};
        for (let i = 0; i < attributeNodes.length; i += 1) {
          const attrName = attributeNodes[i].name;
          if (
            this.currentConfig.vueIgnoreAttrs &&
            this.currentConfig.vueIgnoreAttrs.indexOf(attrName) !== -1
          ) {
            // eslint-disable-next-line
            continue;
          }
          attrs[attrName] = attributeNodes[i].value;
        }
        if (this.$el[0].innerHTML) {
          attrs[this.INNER_HTML_ATTR] = this.$el[0].innerHTML;
        }
        modelContent = attrs;
      } else {
        const returnedHtml = this.f_editor.html.get();
        if (typeof returnedHtml === 'string') {
          modelContent = returnedHtml;
        }
      }
      this.oldModel = modelContent;
      this.$emit('input', modelContent);
    },
    initListeners() {
      // eslint-disable-next-line @typescript-eslint/no-this-alias
      const self = this;
      this.registerEvent('initialized', () => {
        if (self.f_editor.events) {
          // bind contentChange and keyup event to froalaModel
          self.f_editor.events.on('contentChanged', () => {
            self.updateModel();
          });
          if (self.currentConfig.immediateVueModelUpdate) {
            self.f_editor.events.on('keyup', () => {
              self.updateModel();
            });
          }
        }
      });
    },
    // register event on editor element
    registerEvent(eventName, callback) {
      if (!eventName || !callback) {
        return;
      }
      // Initialized event.
      if (eventName === 'initialized') {
        this.initEvents.push(callback);
      } else {
        if (!this.currentConfig.events) {
          this.currentConfig.events = {};
        }
        this.currentConfig.events[eventName] = callback;
      }
    },
    registerEvents() {
      // Handle initialized on its own.
      this.registerInitialized();
      // Get current events.
      const { events } = this.currentConfig;
      if (!events) {
        return;
      }
      // eslint-disable-next-line
      for (var event in events) {
        // eslint-disable-next-line
        if (events.hasOwnProperty(event) && event !== 'initialized') {
          this.registerEvent(event, events[event]);
        }
      }
    },
    registerInitialized() {
      // Bind initialized.
      if (!this.currentConfig.events) {
        this.currentConfig.events = {};
      }
      // Set original initialized event.
      if (this.currentConfig.events.initialized) {
        this.registerEvent(
          'initialized',
          this.currentConfig.events.initialized
        );
      }
      // Bind initialized event.
      this.currentConfig.events.initialized = () => {
        for (let i = 0; i < this.initEvents.length; i += 1) {
          this.initEvents[i].call(this.f_editor);
        }
      };
    },
  },
  render() {
    // eslint-disable-next-line vue/require-slots-as-functions
    return h(this.currentTag, [this.$slots.default]);
  },
};
</script>
