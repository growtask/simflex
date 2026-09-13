<div class="form-control <?= $activeClass ?>">
                    <div class="form-control__file">
                        <div class="form-control__file-area-wrapper drop-area">
                            <label class="form-control__file-area">
                                <input type="hidden" value="<?= $value ?>" name="<?= $name ?>" >
                                <input type="file" name="" accept=""
                                    class="form-control__file-input">
                                <div class="form-control__file-progressbar progressbar">
                                    <div class="progressbar__bg">
                                        <div class="progressbar__progress"></div>
                                    </div>
                                </div>
                                <div class="form-control__file-head">
                                    <div class="form-control__file-title"><?= $activeText ?>
                                    </div>
                                    <div class="form-control__file-size" style="<?= $activeDisplay ?>"><?= $size ?></div>
                                </div>
                                <div class="form-control__file-btns">
                                    <div class="form-control__file-btn BtnPrimarySm BtnIconLeft btn-file-upload">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21 15V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V15M17 8L12 3M12 3L7 8M12 3V15"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Загрузить
                                    </div>
                                    <div class="form-control__file-btn btn-file-choose BtnSecondaryMonoSm BtnIconLeft">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13 7L11.8845 4.76892C11.5634 4.1268 11.4029 3.80573 11.1634 3.57116C10.9516 3.36373 10.6963 3.20597 10.4161 3.10931C10.0992 3 9.74021 3 9.02229 3H5.2C4.0799 3 3.51984 3 3.09202 3.21799C2.71569 3.40973 2.40973 3.71569 2.21799 4.09202C2 4.51984 2 5.0799 2 6.2V7M2 7H17.2C18.8802 7 19.7202 7 20.362 7.32698C20.9265 7.6146 21.3854 8.07354 21.673 8.63803C22 9.27976 22 10.1198 22 11.8V16.2C22 17.8802 22 18.7202 21.673 19.362C21.3854 19.9265 20.9265 20.3854 20.362 20.673C19.7202 21 18.8802 21 17.2 21H6.8C5.11984 21 4.27976 21 3.63803 20.673C3.07354 20.3854 2.6146 19.9265 2.32698 19.362C2 18.7202 2 17.8802 2 16.2V7Z"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Выбрать
                                    </div>
                                    <button
                                        type="button"
                                        class="form-control__file-btn btn-file-reset BtnSecondarySm BtnIconLeft">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path style="fill: #ffffff !important" fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                                fill="#ffffff" />
                                        </svg>
                                        Удалить
                                    </button>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
<input type="hidden" name="<?= $name ?>_old" value="<?= $value ?>" />