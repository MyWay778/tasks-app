import { useSSRContext, mergeProps, unref, withCtx, createTextVNode, computed, ref, onMounted, resolveComponent, createVNode, openBlock, createBlock, createCommentVNode, Fragment, renderList, createSSRApp, h } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderSlot, ssrIncludeBooleanAttr, ssrRenderClass, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { Link, createInertiaApp } from "@inertiajs/vue3";
import axios from "axios";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$4 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`Create Page`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/CreateTask.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const CreateTask = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$1]]);
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: CreateTask
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$3 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`Edit Task`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/EditTask.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const EditTask = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender]]);
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: EditTask
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$2 = {
  __name: "Default",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "min-h-screen bg-gray-50" }, _attrs))}><header class="bg-white shadow-sm border-b border-gray-200"><nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/",
        class: "text-2xl font-bold text-indigo-600"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`To-Do App`);
          } else {
            return [
              createTextVNode("To-Do App")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="flex space-x-8">`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/",
        class: "text-gray-700 hover:text-indigo-600 font-medium"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Главная`);
          } else {
            return [
              createTextVNode("Главная")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></nav></header><main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/Default.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const COMPLETE_STATUS = "complete";
const _sfc_main$1 = {
  __name: "TaskItem",
  __ssrInlineRender: true,
  props: {
    task: Object
    // Передаем объект задачи целиком
  },
  emits: ["toggle", "delete"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const isCompleted = computed(() => props.task.status === COMPLETE_STATUS);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["flex items-center justify-between p-4 bg-white border rounded-xl shadow-sm transition duration-300", {
          "opacity-60 bg-gray-50 border-gray-200": isCompleted.value,
          "border-white": !isCompleted.value
        }]
      }, _attrs))}><div class="flex items-center space-x-4 flex-1 min-w-0"><input type="checkbox"${ssrIncludeBooleanAttr(isCompleted.value) ? " checked" : ""} class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer"><div class="flex-1 min-w-0"><h3 class="${ssrRenderClass([{
        "text-gray-400 line-through": isCompleted.value,
        "text-gray-900": !isCompleted.value
      }, "text-lg font-semibold truncate transition-all"])}">${ssrInterpolate(__props.task.title)}</h3><p class="${ssrRenderClass([{
        "text-gray-400": isCompleted.value,
        "text-gray-500": !isCompleted.value
      }, "text-sm transition-all"])}">${ssrInterpolate(__props.task.description)}</p></div></div><div class="flex items-center ml-4"><button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/TaskItem.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "TaskList",
  __ssrInlineRender: true,
  props: {
    tasks: Array
  },
  setup(__props) {
    const props = __props;
    console.log("Taksk:", props.tasks);
    const localTasks = ref(props.tasks ?? []);
    onMounted(async () => {
      if (!props.tasks) {
        const response = await axios.get("/api/tasks");
        localTasks.value = response.data;
      }
    });
    const deleteTask = (id) => {
      if (confirm("Удалить задачу?")) ;
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(ssrRenderComponent(_sfc_main$2, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="max-w-2xl mx-auto"${_scopeId}><div class="flex items-center justify-between mb-8"${_scopeId}><h2 class="text-2xl font-bold text-gray-800"${_scopeId}>Мои задачи</h2>`);
            _push2(ssrRenderComponent(_component_router_link, {
              to: "/tasks/create",
              href: "/tasks/create",
              class: "flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm cursor-pointer transition"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` + Новая задача `);
                } else {
                  return [
                    createTextVNode(" + Новая задача ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div class="space-y-4"${_scopeId}>`);
            if (localTasks.value.length === 0) {
              _push2(`<div class="text-center py-10 text-gray-500"${_scopeId}> Список задач пуст </div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<!--[-->`);
            ssrRenderList(localTasks.value, (task) => {
              _push2(ssrRenderComponent(_sfc_main$1, {
                task,
                key: task.id,
                onDelete: ($event) => deleteTask(task.id)
              }, null, _parent2, _scopeId));
            });
            _push2(`<!--]--></div></div>`);
          } else {
            return [
              createVNode("div", { class: "max-w-2xl mx-auto" }, [
                createVNode("div", { class: "flex items-center justify-between mb-8" }, [
                  createVNode("h2", { class: "text-2xl font-bold text-gray-800" }, "Мои задачи"),
                  createVNode(_component_router_link, {
                    to: "/tasks/create",
                    href: "/tasks/create",
                    class: "flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm cursor-pointer transition"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" + Новая задача ")
                    ]),
                    _: 1
                  })
                ]),
                createVNode("div", { class: "space-y-4" }, [
                  localTasks.value.length === 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "text-center py-10 text-gray-500"
                  }, " Список задач пуст ")) : createCommentVNode("", true),
                  (openBlock(true), createBlock(Fragment, null, renderList(localTasks.value, (task) => {
                    return openBlock(), createBlock(_sfc_main$1, {
                      task,
                      key: task.id,
                      onDelete: ($event) => deleteTask(task.id)
                    }, null, 8, ["task", "onDelete"]);
                  }), 128))
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/TaskList.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main
}, Symbol.toStringTag, { value: "Module" }));
createServer(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    resolve: (name) => {
      console.log("Данные для страницы:", page.props.tasks);
      const pages = /* @__PURE__ */ Object.assign({ "./Pages/CreateTask.vue": __vite_glob_0_0, "./Pages/EditTask.vue": __vite_glob_0_1, "./Pages/TaskList.vue": __vite_glob_0_2 });
      return pages[`./Pages/${name}.vue`];
    },
    setup({ App, props, plugin }) {
      return createSSRApp({
        render: () => h(App, props)
      }).use(plugin);
    }
  })
);
