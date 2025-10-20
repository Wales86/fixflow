import { useRef, useState, DragEvent, ChangeEvent } from "react";
import { useLaravelReactI18n } from "laravel-react-i18n";
import { Upload, X, Image as ImageIcon } from "lucide-react";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";

interface ImageUploaderProps {
    value: File[] | null;
    onChange: (files: File[] | null) => void;
    disabled?: boolean;
    maxFiles?: number;
    maxSizeInMB?: number;
}

export function ImageUploader({
    value,
    onChange,
    disabled = false,
    maxFiles = 10,
    maxSizeInMB = 5,
}: ImageUploaderProps) {
    const { t } = useLaravelReactI18n();
    const [isDragging, setIsDragging] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const files = value || [];

    const validateFiles = (newFiles: FileList | File[]): File[] | null => {
        const fileArray = Array.from(newFiles);
        const validFiles: File[] = [];

        // Sprawdź liczbę plików
        if (files.length + fileArray.length > maxFiles) {
            setError(
                t("max_files_exceeded", { max: maxFiles.toString() })
            );
            return null;
        }

        // Walidacja każdego pliku
        for (const file of fileArray) {
            // Sprawdź typ pliku
            if (!file.type.startsWith("image/")) {
                setError(t("only_images_allowed"));
                return null;
            }

            // Sprawdź rozmiar pliku
            const fileSizeInMB = file.size / (1024 * 1024);
            if (fileSizeInMB > maxSizeInMB) {
                setError(
                    t("file_too_large", {
                        name: file.name,
                        max: maxSizeInMB.toString(),
                    })
                );
                return null;
            }

            validFiles.push(file);
        }

        setError(null);
        return validFiles;
    };

    const handleFileSelect = (e: ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files.length > 0) {
            const validFiles = validateFiles(e.target.files);
            if (validFiles) {
                onChange([...files, ...validFiles]);
            }
        }
        // Reset input aby umożliwić ponowne wybranie tego samego pliku
        if (fileInputRef.current) {
            fileInputRef.current.value = "";
        }
    };

    const handleDragOver = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        if (!disabled) {
            setIsDragging(true);
        }
    };

    const handleDragLeave = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    };

    const handleDrop = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);

        if (disabled) return;

        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const validFiles = validateFiles(e.dataTransfer.files);
            if (validFiles) {
                onChange([...files, ...validFiles]);
            }
        }
    };

    const handleRemoveFile = (index: number) => {
        const newFiles = files.filter((_, i) => i !== index);
        onChange(newFiles.length > 0 ? newFiles : null);
        setError(null);
    };

    const handleClick = () => {
        if (!disabled && fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    return (
        <div className="space-y-4">
            {/* Drop Zone */}
            <div
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                onClick={handleClick}
                className={cn(
                    "relative cursor-pointer rounded-lg border-2 border-dashed p-8 transition-colors",
                    isDragging && !disabled
                        ? "border-primary bg-primary/5"
                        : "border-border hover:border-primary/50 hover:bg-muted/50",
                    disabled && "cursor-not-allowed opacity-50"
                )}
            >
                <input
                    ref={fileInputRef}
                    type="file"
                    accept="image/*"
                    multiple
                    onChange={handleFileSelect}
                    disabled={disabled}
                    className="hidden"
                />

                <div className="flex flex-col items-center gap-2 text-center">
                    <div className="rounded-full bg-muted p-3">
                        <Upload className="h-6 w-6 text-muted-foreground" />
                    </div>

                    <div className="space-y-1">
                        <p className="text-sm font-medium">
                            {t("click_or_drag_to_upload")}
                        </p>
                        <p className="text-xs text-muted-foreground">
                            {t("supported_formats")}: PNG, JPG, JPEG, GIF, WEBP
                        </p>
                        <p className="text-xs text-muted-foreground">
                            {t("max_file_size")}: {maxSizeInMB}MB •{" "}
                            {t("max_files")}: {maxFiles}
                        </p>
                    </div>
                </div>
            </div>

            {/* Error Message */}
            {error && (
                <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
                    {error}
                </div>
            )}

            {/* File Previews */}
            {files.length > 0 && (
                <div className="space-y-2">
                    <p className="text-sm font-medium">
                        {t("selected_files")} ({files.length}/{maxFiles})
                    </p>

                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                        {files.map((file, index) => (
                            <FilePreview
                                key={`${file.name}-${index}`}
                                file={file}
                                onRemove={() => handleRemoveFile(index)}
                                disabled={disabled}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

interface FilePreviewProps {
    file: File;
    onRemove: () => void;
    disabled?: boolean;
}

function FilePreview({ file, onRemove, disabled }: FilePreviewProps) {
    const { t } = useLaravelReactI18n();
    const [preview, setPreview] = useState<string | null>(null);

    // Generuj podgląd obrazu
    useState(() => {
        const reader = new FileReader();
        reader.onloadend = () => {
            setPreview(reader.result as string);
        };
        reader.readAsDataURL(file);
    });

    const formatFileSize = (bytes: number): string => {
        if (bytes < 1024) return bytes + " B";
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
        return (bytes / (1024 * 1024)).toFixed(1) + " MB";
    };

    return (
        <div className="group relative aspect-square overflow-hidden rounded-lg border bg-muted">
            {preview ? (
                <img
                    src={preview}
                    alt={file.name}
                    className="h-full w-full object-cover"
                />
            ) : (
                <div className="flex h-full w-full items-center justify-center">
                    <ImageIcon className="h-8 w-8 text-muted-foreground" />
                </div>
            )}

            {/* Overlay z nazwą i rozmiarem */}
            <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                <p className="truncate text-xs font-medium text-white">
                    {file.name}
                </p>
                <p className="text-xs text-white/80">
                    {formatFileSize(file.size)}
                </p>
            </div>

            {/* Przycisk usunięcia */}
            {!disabled && (
                <Button
                    type="button"
                    variant="destructive"
                    size="icon"
                    onClick={onRemove}
                    className="absolute right-2 top-2 h-6 w-6 opacity-0 transition-opacity group-hover:opacity-100"
                >
                    <X className="h-4 w-4" />
                    <span className="sr-only">{t("remove_file")}</span>
                </Button>
            )}
        </div>
    );
}
