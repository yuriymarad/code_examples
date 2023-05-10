<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { useSnackbar } from "vue3-snackbar";
import dayjs from "dayjs";

const snackbar = useSnackbar();

const props = defineProps({
    userId: Number,
    unreadNotifications: Number,
});

let counter = ref(props.unreadNotifications);

onMounted(() => {
    window.Echo.private("App.Models.User." + props.userId).notification(
        (notification) => {
            snackbar.add({
                type: "info",
                title: notification.title,
                text: notification.message,
                background: "steelblue",
            });
            counter.value += 1;
        }
    );
});

onUnmounted(() => {
    window.Echo.private("App.Models.User." + props.userId).stopListening(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated"
    );
});

let notifications = ref([]);

const getNotifications = () => {
    axios.get(route("notifications")).then((response) => {
        notifications.value = response.data;
    });
};

const readAll = () => {
    axios.put(route("notifications.read.all")).then((response) => {
        notifications.value = response.data;
        counter.value = 0;
    });
};

const readById = (notification) => {
    if (!notification.data?.link) {
        return;
    }
    axios
        .get(route("notifications.read.id", { id: notification.id }))
        .then(() => {
            window.location.href = notification.data?.link;
        });
};
</script>

<template>

    <vue3-snackbar
        bottom
        right
        :duration="10000"
    >
    </vue3-snackbar>

    <Popover class="relative">
        <PopoverButton
            @click="getNotifications()"
            class="outline-0"
        >
            <strong class="relative inline-flex items-center px-2.5 py-1.5">
                <span class="absolute -top-1 -right-1 h-5 w-5 p-2 rounded-full bg-red-600 flex justify-center items-center items counter text-white"><span>{{counter}}</span></span>
                <span class="ml-1.5 text-white cursor-pointer">
                    <font-awesome-icon icon="bell" />
                </span>
            </strong>
        </PopoverButton>
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <PopoverPanel class="absolute left-1/2 z-10 mt-3 w-[32rem] -translate-x-2/3 transform px-4 sm:px-0 lg:max-w-3xl">
                <div class="overflow-auto bg-white h-[32rem]	 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="relative grid gap-8 bg-white p-7 grid-cols-1 pb-20">
                        <p
                            v-if="!notifications.length"
                            class=" text-gray-500"
                        >
                            {{ $t('Notifications list is empty.') }}
                        </p>
                        <div
                            v-for="item in notifications"
                            :key="item.title"
                            :class="{'cursor-pointer': item.data?.link}"
                            class="-m-3 flex items-center cursor-default rounded-lg p-2 transition duration-150 ease-in-out hover:bg-gray-50
                            focus-visible:ring-orange-500 focus-visible:ring-opacity-50"
                            @click="readById(item)"
                        >
                            <div class="ml-4 flex items-center justify-between">
                                <div>
                                    <p
                                        :class="{'green-color font-semibold underline': item.data?.link}"
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ $t(item.data?.title) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $t(item.data?.message) }}
                                    </p>
                                </div>
                                <div class="text-sm ml-2 whitespace-nowrap flex items-center">
                                    <font-awesome-icon
                                        v-if="!item.read_at"
                                        icon="circle"
                                        class="green-color text-[8px] mr-2"
                                    />
                                    {{ dayjs(item.created_at).locale($page.props.lang).format('DD-MM-YYYY hh:mm A')  }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 fixed bottom-0 w-full">
                        <a
                            href="##"
                            @click="readAll()"
                            class="flow-root text-center rounded-md px-2 py-2 transition duration-150 ease-in-out hover:bg-gray-100 
                            focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50"
                        >
                            <span class="text-sm font-bold text-gray-900 uppercase green-color">
                                {{ $t('Read All Notifications') }}
                            </span>
                        </a>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>

</template>
