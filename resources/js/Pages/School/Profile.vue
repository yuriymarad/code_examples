<script setup>
import { Head, Link } from "@inertiajs/inertia-vue3";
import Footer from "@/Components/Footer.vue";
import Header from "@/Components/Header.vue";
import { ref, nextTick, computed, watchEffect } from "vue";
import Awards from "@/Components/Awards.vue";

const props = defineProps({
    school: Object,
    user: Object,
    awards: Array,
    mentorAwards: Object,
    schoolTypes: String,
    apiKey: String,
    staticMapUrl: String,
});

const center = {
    lat: Number.parseFloat(props.school.lat),
    lng: Number.parseFloat(props.school.lng),
};

const mapContainer = ref(null);
const openMapLink = () => {
    const mapLink = `https://maps.google.com/?ll=${center.lat},${center.lng}`;
    window.open(mapLink, "_blank");
};

const hasMarketingConsent = ref(false);

watchEffect(() => {
    if (window.Cookiebot && window.Cookiebot.consent.marketing) {
        hasMarketingConsent.value = true;
    } else {
        hasMarketingConsent.value = false;
    }
});

nextTick().then(() => {
    if (hasMarketingConsent.value) {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: center,
            zoom: 8,
        });
        new google.maps.Marker({
            position: center,
            map,
        });
    }
});
</script>

<template>

    <Head :title="school.name" />

    <div class="relative bg-gray-100 sm:items-center sm:pt-0">
        <Header></Header>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="w-full flex items-center justify-start gap-4">
                    <div class="relative school-logo">
                        <img
                            :src="school.profile_photo_url"
                            class="rounded-full h-32 w-32 object-cover"
                        >
                    </div>
                    <div>
                        <h1 class="text-4xl font-extrabold blue-color">{{school.name}}</h1>
                    </div>
                </div>

                <div
                    ref="mapContainer"
                    class="my-6 h-80 w-full cursor-pointer"
                    @click="openMapLink"
                    v-if="!hasMarketingConsent"
                >
                    <img
                        :src="staticMapUrl"
                        alt="Static Map"
                        class="w-full h-full object-cover"
                    />
                </div>

                <div
                    class="my-6 h-80"
                    id="map"
                    v-if="hasMarketingConsent"
                >
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="mt-5 lg:col-span-2">
                        <div class="px-4 py-3 bg-gray-100 sm:px-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="details-title info-icon relative">
                                <h2 class="blue-color pl-8 font-bold text-lg">{{ $t('Details') }}</h2>
                            </div>
                        </div>

                        <div class="px-4 py-5 bg-white sm:p-6 shadow-md sm:rounded-bl-md sm:rounded-br-md">
                            <div class="w-full">
                                <div class="flex mb-6">
                                    <div class="md:w-64 text-right pr-2 md:pr-12 text-sky-900">
                                        {{ $t('School Name') }}
                                    </div>
                                    <div>
                                        {{ school.name }}
                                    </div>
                                </div>
                                <div
                                    class="flex mb-6"
                                    v-if="school.country?.name === 'Scotland'"
                                >
                                    <div class="md:w-64 text-right pr-2 md:pr-12 text-sky-900">
                                        {{ $t('School Type') }}
                                    </div>
                                    <div>
                                        {{ schoolTypes }}
                                    </div>
                                </div>
                                <div class="flex mb-6">
                                    <div class="md:w-64 text-right pr-2 md:pr-12 text-sky-900">
                                        {{ $t('School Level') }}
                                    </div>
                                    <div v-if="school.level === 'primary'">
                                        {{ $t('Primary') }}
                                    </div>
                                    <div v-else>
                                        {{ $t('Post Primary') }}
                                    </div>
                                </div>
                                <div class="flex mb-6">
                                    <div class="md:w-64 text-right pr-2 md:pr-12 text-sky-900">
                                        {{ $t('School Address') }}
                                    </div>
                                    <div class="w-80">
                                        {{ school.address_1 }}
                                    </div>
                                </div>
                                <div class="flex mb-6">
                                    <div class="md:w-64 text-right pr-2 md:pr-12 text-sky-900">
                                        {{ $t('Country') }}
                                    </div>
                                    <div>
                                        {{ school.country?.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-10">
                    <Awards :awards="awards"></Awards>
                </div>
                <div
                    class="mt-10"
                    v-if="mentorAwards.length"
                >
                    <Awards
                        :awards="mentorAwards"
                        :title="$t('Mentor programme')"
                    ></Awards>
                </div>
            </div>
        </div>
    </div>

    <Footer></Footer>

</template>