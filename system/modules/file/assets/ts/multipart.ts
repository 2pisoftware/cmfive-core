import { ArrayBuffer as spark } from "~/spark-md5";

const beginMultipartUpload = async (file: File, filename: string = file.name, endpoint = "/file/ajax_multipart"): Promise<string> => {
    const res = await fetch(endpoint, {
        method: "POST",
        body: JSON.stringify({
            filename,
            mime: file.type,
        })
    });

    const json = await res.json();

    return json.id;
};

const upload_part = async (data: number[], upload_id: string, part_number: number, signal: AbortSignal) => {
    if (signal.aborted) throw new Error("aborted");

    const md5 = btoa(spark.hash(data, true));

    const res = await fetch("/file-multipart/ajax_part", {
        method: "POST",
        body: JSON.stringify({
            id: upload_id,
            part: part_number,
            length: data.length,
            md5,
        }),
        signal
    });

    const { endpoint } = await res.json();

    if (!res.ok) throw new Error("failed to get endpoint");

    const up = await fetch(endpoint, {
        method: "PUT",
        body: new Uint8Array(data),
        headers: {
            "Content-MD5": md5,
        },
        signal
    });

    const upres = await up.text();

    if (!up.ok) throw new Error(upres);
};

const sleep = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));
const ATTEMPT_DELAY_SECONDS = 5;

const uploadParts = async (file: File, upload_id: string) => {
    const CHUNK_SIZE = 1024 * 1024 * 5;
    const MAX_RETRIES = 5;

    let i = 1;
    let part: number[] = [];

    let promises: Promise<void>[] = [];

    const abortController = new AbortController();

    const doPartWithRetry = async (part: number[]) => {
        if (abortController.signal.aborted) return;

        let attempts = 0;
        do {
            try {
                attempts++;
                await upload_part(part, upload_id, i, abortController.signal);
                return;
            }
            catch (e) {
                console.log(`failed: ${e.message}`);
                await sleep(ATTEMPT_DELAY_SECONDS * attempts * 1000);
                // we failed. try again until max retries
            }
        } while (attempts < MAX_RETRIES);

        abortController.abort();
    };

    for await (const chunk of file.stream()) {
        if (abortController.signal.aborted) {
            throw new Error("aborted");
        }

        if (part.length > CHUNK_SIZE) {
            const aligned = part.slice(0, CHUNK_SIZE);
            promises.push(doPartWithRetry(aligned));
            i++;
            part = part.slice(CHUNK_SIZE);

            if (promises.length > 3) await Promise.all(promises);
        }

        part = part.concat(Array.from(chunk));
    }

    // and then the remaining section
    promises.push(doPartWithRetry(part));

    await Promise.all(promises);

    if (abortController.signal.aborted)
        throw new Error("aborted");
};

const completeUpload = async (upload_id: string) => {
    const res = await fetch(`/file-multipart/ajax_done/${upload_id}`, {
        method: "POST",
    });

    if (!res.ok) throw new Error("failed");

    return await res.json();
};

const abortUpload = async (upload_id: string) => {
    await fetch(`/file/ajax_multipart/${upload_id}`, {
        method: "DELETE",
    });
};

export { abortUpload, beginMultipartUpload, completeUpload, uploadParts };

