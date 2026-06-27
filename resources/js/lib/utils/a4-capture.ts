/**
 * Shared A4 capture/export utilities.
 *
 * These functions dynamically import `html-to-image` and `jspdf`,
 * capture the rendered A4 element, and save/open the result.
 */

/**
 * Capture the A4 element as a PDF and trigger download.
 *
 * @param a4Ref        - The rendered A4 container element
 * @param setCapturing - Callback to toggle loading state (reactive setter)
 * @param filename     - Output filename (default: 'design-a4.pdf')
 * @param onError      - Optional error handler
 */
export async function captureA4Pdf(
    a4Ref: HTMLElement | null | undefined,
    setCapturing: (v: boolean) => void,
    filename = 'design-a4.pdf',
    onError?: (err: unknown) => void,
): Promise<void> {
    if (!a4Ref) return;

    const { toPng } = await import('html-to-image');
    const { jsPDF } = await import('jspdf');

    setCapturing(true);
    try {
        const dataUrl = await toPng(a4Ref, { pixelRatio: 2 });
        const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
        pdf.addImage(
            dataUrl,
            'PNG',
            0,
            0,
            pdf.internal.pageSize.getWidth(),
            pdf.internal.pageSize.getHeight(),
            undefined,
            'FAST',
        );
        pdf.save(filename);
    } catch (err) {
        console.error('Failed to capture PDF:', err);
        onError?.(err);
    } finally {
        setCapturing(false);
    }
}

/**
 * Capture the A4 element as a PNG and open it in a new tab.
 *
 * @param a4Ref        - The rendered A4 container element
 * @param setCapturing - Callback to toggle loading state (reactive setter)
 * @param tick         - Svelte `tick` function (import from 'svelte')
 * @param title        - Browser tab title for the preview window
 * @param imgStyle     - CSS style for the preview image (default: full width auto height)
 * @param onError      - Optional error handler
 */
export async function previewA4Png(
    a4Ref: HTMLElement | null | undefined,
    setCapturing: (v: boolean) => void,
    tick: () => Promise<void>,
    title = 'Design Preview A4',
    imgStyle = 'max-width:100%;height:auto;',
    onError?: (err: unknown) => void,
): Promise<void> {
    if (!a4Ref) return;

    const { toPng } = await import('html-to-image');

    setCapturing(true);
    await tick();
    await tick();
    try {
        const dataUrl = await toPng(a4Ref, { pixelRatio: 2 });
        const w = window.open('');
        if (w) {
            w.document.write(`<img src="${dataUrl}" style="${imgStyle}" />`);
            w.document.title = title;
        }
    } catch (err) {
        console.error('Failed to capture PNG:', err);
        onError?.(err);
    } finally {
        setCapturing(false);
    }
}
