{{-- 
    Variables expected:
    $prefix      — unique prefix (si, sv, re, pe)
    $radioName   — name for radio inputs
    $hiddenName  — name for hidden input
--}}
<div>
    <label class="wrd-info-label block mb-2">
        Signature <span style="color:#f87171;">*</span>
    </label>

    @if(Auth::user()->signature_path)
        <div style="margin-bottom:16px; padding:12px; background:var(--wr-surface2); border-radius:8px; border:1px solid var(--wr-border);">
            <p style="font-size:13px; color:var(--wr-label); margin-bottom:12px;">Use your saved signature:</p>
            <img src="{{ asset('storage/' . Auth::user()->signature_path) }}"
                 alt="Your Signature"
                 style="max-width:250px; margin-bottom:12px; border:1px solid var(--wr-border); border-radius:4px;">
            <div style="display:flex; gap:16px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="radio" name="{{ $radioName }}" value="saved" checked>
                    <span style="font-size:13px;">Use this signature</span>
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="radio" name="{{ $radioName }}" value="draw">
                    <span style="font-size:13px;">Draw a new one</span>
                </label>
            </div>
        </div>
    @endif

    <div id="{{ $prefix }}-signature-pad-wrap" {{ Auth::user()->signature_path ? 'style=display:none' : '' }}>
        <p style="font-size:13px; color:var(--wr-label); margin-bottom:12px;">Draw your signature below:</p>
        <div style="display:flex; gap:16px; align-items:flex-start;">
            <div>
                <canvas id="{{ $prefix }}-signature-pad" width="400" height="150"
                    style="border:2px solid var(--wr-border); border-radius:8px;
                           background:var(--wr-surface); cursor:crosshair; display:block;">
                </canvas>
                <button type="button" id="{{ $prefix }}-clear-signature"
                        style="margin-top:8px; padding:6px 16px; background:#ef4444; color:#fff;
                               border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                    <i class="fas fa-redo mr-1"></i> Clear
                </button>
            </div>
            <div>
                <p style="font-size:12px; color:var(--wr-label); margin-bottom:8px; font-weight:600;">Preview:</p>
                <img id="{{ $prefix }}-signature-preview" src="" alt="Signature Preview"
                     style="border:2px solid var(--wr-border); border-radius:8px; background:var(--wr-surface);
                            min-width:200px; height:100px; display:none;">
                <div id="{{ $prefix }}-signature-empty"
                     style="border:2px dashed var(--wr-border); border-radius:8px; background:var(--wr-surface2);
                            min-width:200px; height:100px; display:flex; align-items:center;
                            justify-content:center; color:var(--wr-muted); font-size:12px;">
                    Signature preview
                </div>
            </div>
        </div>
    </div>

    <input type="hidden"
           name="{{ $hiddenName }}"
           id="{{ $prefix }}-signature-output"
           value="{{ Auth::user()->signature_path ? asset('storage/' . Auth::user()->signature_path) : '' }}">
</div>