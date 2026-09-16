import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface SharePointFile {
    folder_name: string;
    file_name: string;
    folder_path: string;
    file_size: string;
    mime_type: string;
    last_modified: string;
    status: string;
}

interface SharePointResponse {
    success: boolean;
    data: SharePointFile[];
    total: number;
}

export default function Files() {
    const [files, setFiles] = useState<SharePointFile[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadFiles = async () => {
            try {
                setLoading(true);
                setError(null);

                const response = await fetch('/sharepoint/files');

                if (!response.ok) {
                    throw new Error('Unable to retrieve SharePoint files.');
                }

                const result: SharePointResponse = await response.json();

                if (!result.success) {
                    throw new Error('Unable to retrieve SharePoint files.');
                }

                setFiles(result.data);
            } catch (err) {
                setError(
                    err instanceof Error
                        ? err.message
                        : 'Something went wrong while loading files.',
                );
            } finally {
                setLoading(false);
            }
        };

        loadFiles();
    }, []);

    return (
        <>
            <Head title="Files" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex-1 overflow-hidden rounded-xl border">
                    <div className="p-6">
                        <div className="mb-6">
                            <h1 className="text-2xl font-semibold">
                                Files
                            </h1>

                            <p className="mt-1 text-sm text-muted-foreground">
                                Files detected in the SharePoint Input folder.
                            </p>
                        </div>

                        {loading && (
                            <div className="rounded-lg border p-6 text-center text-sm text-muted-foreground">
                                Loading files from SharePoint...
                            </div>
                        )}

                        {error && (
                            <div className="rounded-lg border p-6 text-center text-sm text-red-600">
                                {error}
                            </div>
                        )}

                        {!loading && !error && files.length === 0 && (
                            <div className="rounded-lg border p-6 text-center text-sm text-muted-foreground">
                                No files found.
                            </div>
                        )}

                        {!loading && !error && files.length > 0 && (
                            <div className="overflow-x-auto rounded-lg border">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b bg-muted/50">
                                            <th className="px-4 py-3 text-left font-medium">
                                                Folder Name
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                File Name
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                File Size
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                Folder Path
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                MIME Type
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                Last Modified
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {files.map((file, index) => (
                                            <tr
                                                key={`${file.folder_path}-${file.file_name}-${index}`}
                                                className="border-b last:border-b-0"
                                            >
                                                <td className="px-4 py-3">
                                                    {file.folder_name}
                                                </td>

                                                <td className="px-4 py-3 font-medium">
                                                    {file.file_name}
                                                </td>

                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    {file.file_size}
                                                </td>

                                                <td className="px-4 py-3">
                                                    {file.folder_path || 'Input'}
                                                </td>

                                                <td className="px-4 py-3">
                                                    {file.mime_type}
                                                </td>

                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    {file.last_modified}
                                                </td>

                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium">
                                                        {file.status}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}

                        {!loading && !error && files.length > 0 && (
                            <div className="mt-4 text-sm text-muted-foreground">
                                Total files: {files.length}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

Files.layout = {
    breadcrumbs: [
        {
            title: 'Files',
            href: '/files',
        },
    ],
};