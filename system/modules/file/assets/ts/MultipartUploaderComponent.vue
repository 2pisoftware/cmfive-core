<script setup lang="ts">
import * as s3 from "./multipart.js";
import { ref, computed, useTemplateRef } from "../../../../templates/base/node_modules/vue";

import LocalFilePreview from "./LocalFilePreview.vue";

const props = defineProps<{
    endpoint: string;
}>();

const endpoint = computed(() => props.endpoint.length ? props.endpoint : undefined);

const upload = async (e: SubmitEvent) => {
    const target = e.target as HTMLFormElement;

    // for some reason, when no files are selected, this returns a [File] with 0 size
    // I believe vue is somehow messing with it, but I have no idea why
    files.value = new FormData(target).getAll("files") as File[];

    files.value = files.value.filter(x => x.size);

    if (!files.value.length) return;

    done.value = false;
    uploading.value = true;
    failed_count.value = 0;
    success_count.value = 0;
    progress.value = 0;

    disableUpload.value = true;
    disableSubmit.value = true;

    for (const file of files.value) {
        const upload_id = await s3.beginMultipartUpload(file, endpoint.value);

        progress.value++;

        try {
            await s3.uploadParts(file, upload_id);
        }
        catch (e) {
            await s3.abortUpload(upload_id);
            failed_count.value++;
            continue;
        }

        progress.value++;

        await s3.completeUpload(upload_id);

        progress.value++;
        success_count.value++;
    }

    disableUpload.value = false;
    done.value = true;
};

const updateFilePreview = (event: ChangeEvent<HTMLInputElement>) => {
    disableSubmit.value = false;
    files.value = [...event.target.files];
};

type FileWithProgress = File & {
    progress?: number;
    done?: boolean;
};

// can you tell I don't enjoy state
const files = ref<FileWithProgress[]>([]);
const uploading = ref(false);
const done = ref(false);
const failed_count = ref(0);
const success_count = ref(0);
const progress = ref(0);
const total_progress = computed(() => files.value.length * 3);
const disableSubmit = ref(true);
const disableUpload = ref(false);
</script>

<template>
    <div class="panel">
        <div v-if="uploading">
            <div class="progress mb-2">
                <div class="progress-bar" :style="`width: ${(progress / total_progress) * 100}%;`" role="progressbar"
                    :aria-valuenow="progress" aria-valuemin="0" :aria-valuemax="(total_progress)">
                </div>
            </div>
            <p>Uploading {{ success_count + failed_count }} / {{ files.length }}</p>
            <p v-if="failed_count">Failed: {{ failed_count }}</p>

            <p v-if="done">Finished uploading</p>
        </div>

        <form @submit.prevent="upload">
            <fieldset id="multipart_uploader_fieldset" class="d-flex gap-2 align-items-center pt-0">
                <label for="multipart_uploader_files" :class="disableUpload ? 'opacity-50' : ''" style="cursor: pointer">
                    <p class="mb-0 form-control">Select Files <i class="bi bi-cloud-arrow-up"></i></p>
                </label>
                <input id="multipart_uploader_files" :disabled="disableUpload" @change="updateFilePreview" name="files" type="file" multiple
                    hidden>
                <input id="multipart_uploader_submit" :disabled="disableSubmit" type="submit" class="btn btn-primary" value="Upload"
                    style="color: white">
            </fieldset>
        </form>

        <div v-if="files.length">
            <h5 class="mt-2 mb-3 pb-2 border-bottom">Selected</h5>
            <div class="d-flex gap-1 flex-wrap">
                <LocalFilePreview :file="file" v-for="file in files.slice(0, 5)" style="max-height: 200px;">
                </LocalFilePreview>
            </div>
            <p v-if="files.length > 5" class="d-flex justify-content-center align-items-center">Additional {{
                files.length - 5 }} files not previewed.</p>
        </div>
    </div>
</template>